<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'price'
    ];

    /**
     * Get all orders attached to this product.
     */
    public function orders(): HasMany {
        return $this->hasMany(Order::class);
    }

    /**
     * Get all stocks attached to this product.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }
}
