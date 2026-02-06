<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhoneModelUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow for now; adjust authorization if needed
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $id = $this->route('id');

        return [
            'name' => 'required|string|max:255|unique:phone_models,model_name,' . $id,
            'brand_id' => 'required|exists:brands,id',
            'processor' => 'required|string',
            'battery_capacity' => 'required|integer',
            'release_year' => 'nullable|digits:4',
            'description' => 'nullable|array',
            'description.*.key' => 'nullable|string',
            'description.*.value' => 'nullable|string',
            'image' => 'nullable|array',
            'image.*' => 'nullable|string',
        ];
    }


    public function messages()
    {
        return [
            'name.required' => 'The model name field is required.',
            'name.unique' => 'The model name has already been taken.',
            'brand_id.required' => 'The brand field is required.',
            'brand_id.exists' => 'The selected brand is invalid.',
            'processor.required' => 'The processor field is required.',
            'battery_capacity.required' => 'The battery capacity field is required.',
            'battery_capacity.integer' => 'The battery capacity must be an integer.',
            'release_year.digits' => 'The release year must be a 4 digit year.',
            'image.array' => 'The image field must be an array.',
            'image.*.string' => 'Each image must be a valid base64 string or filename.',
        ];
    }
}
