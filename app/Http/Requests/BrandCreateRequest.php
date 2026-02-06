<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'brand_name' => 'required|string|max:255|unique:brands,brand_name',
            'logo' => 'nullable|image',
        ];
    }

    public function messages(): array
    {
        return [
            'brand_name.required' => 'The brand name field is required.',
            'brand_name.string' => 'The brand name must be a string.',
            'brand_name.max' => 'The brand name may not be greater than 255 characters.',
            'logo.image' => 'The logo must be an image file.',
        ];
    }
}
