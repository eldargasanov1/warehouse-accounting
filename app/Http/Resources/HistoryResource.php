<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'before' => $this->before,
            'after' => $this->after,
            'created_at' => $this->created_at,
            'stock' => StockResource::make($this->whenLoaded('stock'))
        ];
    }
}
