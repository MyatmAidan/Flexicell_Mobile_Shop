<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallmentRateCreateRequest extends FormRequest
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
            'installment_month' => 'required|integer|min:1|max:60',
            'installment_rate' => 'required|numeric|min:0|max:100',
        ];
    }

    public function messages()
    {
        return [
            'installment_month.required' => 'The installment month field is required.',
            'installment_month.integer' => 'The installment month must be an integer.',
            'installment_month.min' => 'The installment month must be at least 1.',
            'installment_month.max' => 'The installment month may not be greater than 60.',
            'installment_rate.required' => 'The installment rate field is required.',
            'installment_rate.numeric' => 'The installment rate must be a number.',
            'installment_rate.min' => 'The installment rate must be at least 0%.',
            'installment_rate.max' => 'The installment rate may not be greater than 100%.',
        ];
    }
}
