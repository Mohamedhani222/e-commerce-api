<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => "required|string",
            'image' => "required|string",
            'price' => "required|decimal:2",
            'countInStock' => "required|numeric",
            'category_id' => 'required'
        ];
    }
}
