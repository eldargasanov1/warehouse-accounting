<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class History extends Model
{
    protected $fillable = [
        'stock_id',
        'before',
        'after'
    ];

    /**
     * Get stock attached to this history.
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    #[Scope]
    protected function filterHistory(Builder $query, array $filter): Builder
    {
        foreach ($filter as $key => $value) {
            $query = match ($key) {
                'product_id' => $query->whereRelation('stock', 'product_id', '=', $value),
                'warehouse_id' => $query->whereRelation('stock', 'warehouse_id', '=', $value),
                'created_at' => $query->whereDate('created_at', $value),
                default => $query
            };
        }

        return $query;
    }
}
