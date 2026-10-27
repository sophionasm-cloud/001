<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'profile_picture',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get profile picture URL or fallback to generated avatar.
     */
    public function avatarUrl(): string
    {
        if ($this->profile_picture && file_exists(storage_path('app/public/' . $this->profile_picture))) {
            return asset('storage/' . $this->profile_picture);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random&size=128';
    }

    public function hasRole(string $name): bool
    {
        return $this->role?->name === $name;
    }

    /** Where this user lands after login/registration. */
    public function homeRoute(): string
    {
        return match ($this->role?->name) {
            'Super Admin' => route('admin.dashboard'),
            'Vendor'      => route('vendor.dashboard'),
            default       => route('customer.dashboard'),
        };
    }
}


