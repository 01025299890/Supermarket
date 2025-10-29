<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Models\Rate;
use Str;
// use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use BaconQrCode\Renderer\ImageRenderer;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->has('type')) {
            $products = Product::where('type', 'like', '%' . $request->input('type') . '%')->get();
            return response()->json($products);
        }
        $products = Product::with('rates')->get();
        return response()->json($products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $validated['image'] = $path;
            $imageUrl = asset('storage/' . $path);
        }
        $validated['user_id'] = auth()->user()->id;
        $product = Product::create($validated);
        
    $qrCodeValue = Str::uuid();
    $product->qr_code = $qrCodeValue;
    $product->save();
        if (!file_exists(public_path('qrcodes'))) {
            mkdir(public_path('qrcodes'), 0777, true);
        }
        $qrCode = QrCode::create($qrCodeValue)
            ->setSize(300)
            ->setMargin(10);

        // $qrPath = public_path('qrcodes/' . $qrCodeValue . '.png');
        // config(['qrcode.default_writer' => 'gd']);

        $writer = new PngWriter();
        $writer->write($qrCode)->saveToFile(public_path('qrcodes/' . $qrCodeValue . '.png'));


        return response()->json([
            'image_url' => $imageUrl ?? null,
            'qr_url' => asset('qrcodes/' . $qrCodeValue . '.png'),
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        if($request->has('rate') ){
            if(
                Rate::where('user_id', auth()->id())->where('product_id', $id)->exists()
            ){
                Rate::where('user_id', auth()->id())->where('product_id', $id)->update([
                    'rating' => $request->input('rate'),
                    'review' => $request->input('review', null),
                ]);
                $avg_rate = Rate::where('product_id', $id)->avg('rating');
                Product::where('id', $id)->update([
                    'rating' => $avg_rate
                ]);
                $rate = Rate::where('user_id', auth()->id())->where('product_id', $id)->first();
                return response()->json(['message' => 'Rating updated successfully', 'rate' => $rate]);
            }
            else{
            $rate = Rate::create([
                'user_id' => auth()->id(),
                'product_id' => $id,
                'rating' => $request->input('rate'),
                'review' => $request->input('review', null),
            ]);
            $avg_rate = Rate::where('product_id', $id)->avg('rating');
            $num_rate = Rate::where('product_id', $id)->count();
            Product::where('id', $id)->update([
                'rating' => $avg_rate
            ]);
            return response()->json(['message' => 'Rating submitted successfully', 'rate' => $rate,'number_of_ratings'=>$num_rate]);
            }
        }
        $product = Product::with('rates')->findOrFail($id);
        return response()->json($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'image' => 'nullable|image|max:2048',
            'type' => 'sometimes|string|max:255',
            'brand' => 'nullable|string|max:255',
            'name' => 'sometimes|string|max:255',
            'unit_quantity' => 'sometimes|numeric|min:1',
            'unit' => 'sometimes|max:50|in:kg,g,l,ml,pcs',
            'price' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string|max:2000',
            'available_quantity' => 'sometimes|integer|min:0',
            
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $validated['image'] = $path;
            $imageUrl = asset('storage/' . $path);
        } else {
            $imageUrl = null;
        }

        // $validated['user_id'] = auth()->id();

        $product->update($validated);

        return response()->json([
            'image_url' => $imageUrl ?? null,
            'message' => 'Product updated successfully',
            'product' => $product
        ],200);
    } 
    


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }

    // public function search(Request $request)
    // {
    //     $query = Product::query();

    //     if ($request->has('type')) {
    //         $query->where('type', 'like', '%' . $request->input('type') . '%');
    //     }

    //     if ($request->has('brand')) {
    //         $query->where('brand', 'like', '%' . $request->input('brand') . '%');
    //     }

    //     if ($request->has('name')) {
    //         $query->where('name', 'like', '%' . $request->input('name') . '%');
    //     }

    //     if ($request->has('min_price')) {
    //         $query->where('price', '>=', $request->input('min_price'));
    //     }

    //     if ($request->has('max_price')) {
    //         $query->where('price', '<=', $request->input('max_price'));
    //     }

    //     $products = $query->get();

    //     return response()->json($products);
    // }

    // public function rate(Request $request, $id)
    // {
    //     $request->validate([
    //         'rating' => 'numeric|min:0.1|max:5',
    //     ]);

    //     $product = Product::findOrFail($id);
    //     $user = auth()->user();

    //     // Check if the user has already rated the product
    //     $existingRating = $product->ratings()->where('user_id', $user->id)->first();
    //     if ($existingRating) {
    //         return response()->json(['message' => 'You have already rated this product'], 400);
    //     }

    //     // Create a new rating
    //     $product->ratings()->create([
    //         'user_id' => $user->id,
    //         'rating' => $request->input('rating'),
    //     ]);

    //     // Update the product's average rating
    //     $averageRating = $product->ratings()->avg('rating');
    //     $product->average_rating = $averageRating;
    //     $product->save();

    //     return response()->json(['message' => 'Rating submitted successfully', 'average_rating' => $averageRating]);
    // }

    public function searchByQR_index()
    {
        return view('qr_code_search');
    }

    public function searchByQR(Request $request)
    {
        $code = $request->query('code');
        $product = Product::where('qr_code', $code)->first();

        return response()->json([
            'product' => $product
        ]);
    }

}
