<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CopunRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'code' => 'required|max:8',
            'discount'=> 'required|numeric|max:90',
            "expired_at" => 'required|date_format:Y-m-d H:i:s|after:' . now()
        ];
    }
}
