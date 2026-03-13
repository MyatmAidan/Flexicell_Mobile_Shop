<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'max:15', 'regex:/^0[0-9]{6,11}$/'],
            'address' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'in:admin,user']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'name.max' => 'Name must not exceed 255 characters.',

            'email.required' => 'Email is required.',
            'email.unique' => 'This email is already registered.',
            'email.max' => 'Email must not exceed 255 characters.',

            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',

            'phone.regex' => 'Phone number must start with 0 and contain 7-12 digits.',
            'phone.max' => 'Phone number must not exceed 15 digits.',

            'address.max' => 'Address must not exceed 255 characters.',

            'role.required' => 'Please select a role.',
        ];
    }
}
