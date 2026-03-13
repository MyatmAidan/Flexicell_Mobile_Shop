<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone_model_id' => 'required|exists:phone_models,id',
            'product_type' => 'required|in:new,second hand',
            'selling_price' => 'required|numeric|min:0',
            'warranty_month' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'stock_quantity' => 'nullable|integer|min:0',
            'image' => 'nullable|array',
            'image.*' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'phone_model_id.required' => 'Please select a phone model.',
            'phone_model_id.exists' => 'The selected phone model is invalid.',
            'product_type.required' => 'Product type is required.',
            'product_type.in' => 'Product type must be either new or second hand.',
            'selling_price.required' => 'Selling price is required.',
            'selling_price.numeric' => 'Selling price must be a number.',
        ];
    }
}
