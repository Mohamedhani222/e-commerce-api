<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Order_item_Resource extends JsonResource
{

    public function toArray(Request $request): array
    {

        return [
            'total_price' => $this->total_price,
            'product' => new ProductResource($this->whenLoaded('product')),
            'unit_price' => $this->unit_price,
            'quantity' => $this->quantity

        ];
    }
}
