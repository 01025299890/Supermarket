<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'image',
        'qr_code',
        'type',
        'brand',
        'name',
        'unit_quantity',
        'unit',
        'price',
        'description',
        'available_quantity',
        'rating',
        'user_id',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customers()
    {
        return $this->belongsToMany(User::class, 'customer_product')
                    ->withPivot('quantity', 'price_at_purchase')
                    ->withTimestamps();
    }

    public function rates()
    {
        return $this->hasMany(Rate::class, 'product_id');
    }
}
