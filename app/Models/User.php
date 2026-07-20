<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'mobile',
        'email',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'mobile_verified_at' => 'datetime',
            'blocked_at'         => 'datetime',
            'password'           => 'hashed',
            'is_admin'           => 'boolean',
        ];
    }

    public function isBlocked(): bool
    {
        return ! is_null($this->blocked_at);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class)->orderByDesc('is_default');
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(Address::class)->ofMany([], function ($query) {
            $query->where('is_default', true);
        });
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function hasWishlisted(int $productId): bool
    {
        return $this->wishlists->contains('product_id', $productId);
    }
}
