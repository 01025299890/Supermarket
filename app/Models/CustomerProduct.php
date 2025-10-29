<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerProduct extends Model
{
    protected $table = 'customer_product';
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'price_at_purchase',
        'total_price',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
