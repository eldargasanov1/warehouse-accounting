<?php

namespace App\Listeners;

use App\Events\StockUpdated;
use App\Events\StockUpdating;
use App\Models\History;

class RecordStockUpdateInHistory
{
    /**
     * Stores id of record created by "StockUpdating" event.
     * */
    protected static int $historyId = 0;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StockUpdating|StockUpdated $event): void
    {
        if ($event instanceof StockUpdating) {
            $stock = $event->stock;
            self::$historyId = History::query()->create([
                'stock_id' => $stock->id,
                'before' => $stock->getOriginal('stock')
            ])->id;
        }
        if ($event instanceof StockUpdated) {
            $stock = $event->stock;
            History::query()->find(self::$historyId)->update([
                'after' => $stock->stock
            ]);
        }
    }
}
