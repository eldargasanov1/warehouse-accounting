<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'customer',
        'warehouse_id',
        'status',
        'completed_at'
    ];

    /**
     * Get order's products.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('count');
    }

    /**
     * Get order's warehouse.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Scope a query by filter.
     */
    #[Scope]
    protected function filterOrders(Builder $query, array $filter): Builder
    {
        foreach ($filter as $key => $value) {
            $query = match ($key) {
                'id' => $query->whereIn('id', $value),
                'customer' => $query->where('customer', 'like', "%$value%"),
                'warehouse_id' => $query->whereIn('warehouse_id', $value),
                'status' => $query->where('status', $value),
                'completed_at' => $query->where('completed_at', $value),
                default => $query
            };
        }

        return $query;
    }
}
