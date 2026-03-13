<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeviceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $deviceId = $this->route('id');

        return [
            'product_id' => 'required|exists:products,id',
            'imei' => [
                'required',
                'string',
                Rule::unique('devices', 'imei')->ignore($deviceId),
            ],
            'ram' => 'required|string|max:50',
            'storage' => 'required|string|max:50',
            'color' => 'required|string|max:50',
            'battery_percentage' => 'required|integer|min:0|max:100',
            'condition_grade' => 'required|string|max:20',
            'status' => 'nullable|in:available,sold,reserved,defective',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product.',
            'product_id.exists' => 'The selected product is invalid.',
            'imei.required' => 'IMEI is required.',
            'imei.unique' => 'This IMEI has already been registered.',
        ];
    }
}
