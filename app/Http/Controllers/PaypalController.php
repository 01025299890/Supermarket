<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PayPalClient;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use App\Models\CustomerProduct;
use App\Models\Product;

class PaypalController extends Controller
{
    // إنشاء طلب دفع
    public function createPayment(Request $request)
    {
        $client = PayPalClient::client();

        $userId = auth()->id() ?? null;
        $product = Product::findOrFail($request->product_id);
        $quantity = $request->amount ?? 1;
        $total = $product->price * $quantity;

        $order = new OrdersCreateRequest();
        $order->prefer('return=representation');
        $order->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "reference_id" => "{$request->product_id}:{$userId}:{$quantity}",
                    "description" => "Purchase of {$quantity} x {$product->name}",
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => number_format($total, 2, '.', ''),
                        "breakdown" => [
                            "item_total" => [
                                "currency_code" => "USD",
                                "value" => number_format($total, 2, '.', '')
                            ]
                        ]
                    ],
                    "items" => [
                        [
                            "name" => $product->name,
                            "quantity" => $quantity,
                            "unit_amount" => [
                                "currency_code" => "USD",
                                "value" => number_format($product->price, 2, '.', '')
                            ]
                        ]
                    ]
                ]
            ],
            "application_context" => [
                "return_url" => url('/api/paypal-success'),
                "cancel_url" => url('/api/paypal-cancel'),
            ]
        ];

        $response = $client->execute($order);

        foreach ($response->result->links as $link) {
            if ($link->rel === 'approve') {
                return response()->json([
                    'approval_url' => $link->href,
                    'order_id' => $response->result->id,
                ]);
            }
        }

        return response()->json(['error' => 'Approval URL not found'], 500);
    }

    public function success(Request $request)
    {
        $client = PayPalClient::client();
        $orderId = $request->query('token');

        try {
            $capture = new OrdersCaptureRequest($orderId);
            $capture->prefer('return=representation');
            $response = $client->execute($capture);

            $result = $response->result;
            $reference = $result->purchase_units[0]->reference_id ?? null;
            [$productId, $userId, $quantity] = array_pad(explode(':', $reference), 3, null);

            $product = Product::findOrFail($productId);
            $product->available_quantity -= $quantity;
            $product->save();
            $customerProducts = CustomerProduct::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price_at_purchase' => $product->price,
                'total_price' => $product->price * $quantity,
            ]);

            return response()->json([
                'status' => $result->status,
                'transaction_id' => $result->purchase_units[0]->payments->captures[0]->id ?? null,
                'payer_email' => $result->payer->email_address ?? null,
                'amount' => $result->purchase_units[0]->payments->captures[0]->amount->value
                    . ' ' . $result->purchase_units[0]->payments->captures[0]->amount->currency_code,
                'customer_product' => $customerProducts
            ]);

        } catch (\Exception $e) {
            \Log::error("PayPal Capture Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function cancel()
    {
        return response()->json(['message' => 'Payment cancelled']);
    }
}
