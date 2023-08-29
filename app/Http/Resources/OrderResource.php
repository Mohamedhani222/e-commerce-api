<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isAdmin = Auth::guard('sanctum')->user()->hasRole('SuperAdmin');

        $resourceArray = [
            'id' => $this->id,
            'total_price' => $this->total_price,
            'is_finished' => $this->is_finished,
            'is_paid' => $this->is_paid,
            'paid_at' => $this->paid_at,
            'status' =>$this->status,
            'order_items' => Order_item_Resource::collection($this->whenLoaded('order_items'))
        ];

        if ($isAdmin && $request->is('api/orders*')) {
            $resourceArray['user'] = new UserResource($this->whenLoaded('user_order'));
        }

        return $resourceArray;

    }
}
