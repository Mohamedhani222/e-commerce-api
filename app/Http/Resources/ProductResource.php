<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProductResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $user = Auth::guard('sanctum')->user();
        if ($user->hasRole('SuperAdmin')) {

            return [
                "id" => $this->id,
                "user" => UserResource::make($this->user),
                "name" => $this->name,
                "description" => $this->description,
                "image" => $this->image,
                "price" => $this->price,
                "countInStock" => $this->countInStock,
                "category" => $this->category()->pluck('name'),
            ];

        }
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "image" => $this->image,
            "price" => $this->price,
            "countInStock" => $this->countInStock,
            "category" => $this->category()->pluck('name'),
        ];
    }
}
