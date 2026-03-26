<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeviceCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'imei' => 'required|string|unique:devices,imei',
            'ram_option_id' => 'required|exists:ram_options,id',
            'storage_option_id' => 'required|exists:storage_options,id',
            'color_option_id' => 'required_without:new_color_name|nullable|exists:color_options,id',
            'new_color_name' => 'nullable|string|max:255',
            'new_color_value' => 'nullable|string|max:7',
            'battery_percentage' => 'required|integer|min:0|max:100',
            'condition_grade' => 'required|string|max:20',
            'status' => 'nullable|in:available,sold,reserved,defective',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product.',
            'product_id.exists' => 'The selected product is invalid.',
            'imei.required' => 'IMEI is required.',
            'imei.unique' => 'This IMEI has already been registered.',
            'ram_option_id.required' => 'RAM is required.',
            'storage_option_id.required' => 'Storage is required.',
            'color_option_id.required' => 'Color is required.',
        ];
    }
}
