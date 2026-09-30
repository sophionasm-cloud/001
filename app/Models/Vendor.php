<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'user_id',
        'store_name',
        'description',
        'logo',
        'approval_status',
        'payout_details',
    ];

    protected $casts = [
        'payout_details' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(OrderItem::class)->distinct();
    }
}