<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogCreateRequest extends FormRequest
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
            'title'              => 'required|string|max:255',
            'main_image_data'    => 'nullable|string',
            'section_titles'     => 'required|array|min:1',
            'section_titles.*'   => 'required|string|max:255',
            'section_contents'   => 'required|array|min:1',
            'section_contents.*' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string'   => 'The title must be a string.',
            'title.max'      => 'The title may not be greater than 255 characters.',
            'main_image_data.string' => 'The main image data must be a string.',
            'section_titles.required' => 'At least one section title is required.',
            'section_titles.array'    => 'The section titles must be an array.',
            'section_titles.*.required' => 'Each section title is required.',
            'section_titles.*.string'   => 'Each section title must be a string.',
            'section_titles.*.max'      => 'Each section title may not be greater than 255 characters.',
            'section_contents.required' => 'At least one section content is required.',
            'section_contents.array'    => 'The section contents must be an array.',
            'section_contents.*.required' => 'Each section content is required.',
            'section_contents.*.string'   => 'Each section content must be a string.',
        ];
    }
}
