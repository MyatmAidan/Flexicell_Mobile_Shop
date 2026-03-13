<?php

namespace App\Http\Requests;

use Egulias\EmailValidator\Result\ValidEmail;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'email' => ['required', new ValidEmail, 'max:255', 'unique:users,email'],
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
            'email.required' => 'Email is required.',
            'email.unique' => 'This email already exists.',

            'phone.regex' => 'Phone number format is invalid.',
            'phone.max' => 'Phone number must not exceed 15 digits.',

            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',

            'address.max' => 'Address must not exceed 255 characters.',

            'role.required' => 'Role must be selected.',
        ];
    }
}
