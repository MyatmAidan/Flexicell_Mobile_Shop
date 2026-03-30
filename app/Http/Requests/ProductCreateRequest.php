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
        $rules = [
            'phone_model_id' => 'required|exists:phone_models,id',
            'product_type' => 'required|in:new,second hand',
            'warranty_id' => 'nullable|exists:warranties,id',
            'description' => 'nullable|string',
            'stock_quantity' => 'nullable|integer|min:0',
            'image' => 'nullable|array',
            'image.*' => 'nullable|string',
        ];

        // Conditional rules for second-hand
        if ($this->input('product_type') === 'second hand') {
            $rules = array_merge($rules, [
                'imei' => 'required|unique:devices,imei',
                'ram_option_id' => 'required|exists:ram_options,id',
                'storage_option_id' => 'required|exists:storage_options,id',
                'color_option_id' => 'required|exists:color_options,id',
                'battery_percentage' => 'required|integer|min:0|max:100',
                'condition_grade' => 'required|string',
                'buy_price' => 'required|numeric|min:0',
                'purchase_at' => 'required|date',
            ]);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'phone_model_id.required' => 'Please select a phone model.',
            'phone_model_id.exists' => 'The selected phone model is invalid.',
            'product_type.required' => 'Product type is required.',
            'product_type.in' => 'Product type must be either new or second hand.',
            // Second-hand messages
            'imei.required' => 'IMEI is required for second-hand devices.',
            'imei.unique' => 'This IMEI already exists in the system.',
            'ram_option_id.required' => 'RAM is required for second-hand devices.',
            'storage_option_id.required' => 'Storage is required for second-hand devices.',
            'color_option_id.required' => 'Color is required for second-hand devices.',
            'battery_percentage.required' => 'Battery % is required for second-hand devices.',
            'battery_percentage.integer' => 'Battery % must be a number.',
            'battery_percentage.min' => 'Battery % cannot be less than 0.',
            'battery_percentage.max' => 'Battery % cannot exceed 100.',
            'condition_grade.required' => 'Condition grade is required for second-hand devices.',
            'buy_price.required' => 'Buy price is required for second-hand devices.',
            'buy_price.numeric' => 'Buy price must be a valid number.',
            'purchase_at.required' => 'Purchase date is required for second-hand devices.',
            'purchase_at.date' => 'Purchase date must be a valid date.',
        ];
    }
}
