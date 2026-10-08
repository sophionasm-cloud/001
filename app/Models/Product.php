<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
    'vendor_id',
    'category_id',
    'name',
    'description',
    'cost_price',
    'selling_price',
    'stock',
    'is_active',
    'image',  
];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;

    }
    public function getPriceAttribute()
{
    return $this->selling_price;
}
}
