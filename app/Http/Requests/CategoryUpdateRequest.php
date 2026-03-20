<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryUpdateRequest extends FormRequest
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
        \Log::info("ID is " . $this->route("id")); return [
            'category_name' => 'required|string|max:255|unique:categories,category_name,' . $this->route('id'),
            'color' => 'nullable|string|max:7',
        ];
    }

    public function messages(): array
    {
        \Log::info("ID is " . $this->route("id")); return [
            'category_name.required' => 'Category name is required.',
            'category_name.string' => 'Category name must be a string.',
            'category_name.max' => 'Category name must not exceed 255 characters.',
            'category_name.unique' => 'Category name must be unique.',
        ];
    }
}
