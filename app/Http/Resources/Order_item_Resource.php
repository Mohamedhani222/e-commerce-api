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
            'id' =>$this->id,
            'total_price' => $this->total_price,
            'product' => ProductResource::make($this->whenLoaded('product')),
            'unit_price' => $this->unit_price,
            'quantity' => $this->quantity

        ];
    }
}
