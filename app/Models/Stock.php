<?php

namespace App\Models;

use App\Events\StockUpdated;
use App\Events\StockUpdating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    /** @use HasFactory<\Database\Factories\StockFactory> */
    use HasFactory;

    protected $dispatchesEvents = [
        'updating' => StockUpdating::class,
        'updated' => StockUpdated::class,
    ];

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'stock',
    ];

    /**
     * Get product attached to this stock.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get warehouse attached to this stock.
     */
    public function warehouse(): BelongsTo {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get histories attached to this stock.
     */
    public function histories(): HasMany
    {
        return $this->hasMany(History::class);
    }
}
