<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8'],
            'phone'    => ['nullable', 'max:15', 'regex:/^0[0-9]{6,11}$/'],
            'address'  => ['nullable', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:50', Rule::unique('users', 'username')->ignore($userId)],
            'role'     => ['required', 'string', 'in:superadmin,manager,staff,user'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.unique'   => 'This email already exists.',
            'phone.regex'    => 'Phone number format is invalid.',
            'phone.max'      => 'Phone number must not exceed 15 digits.',
            'password.min'   => 'Password must be at least 8 characters.',
            'address.max'    => 'Address must not exceed 255 characters.',
            'role.required'  => 'Role must be selected.',
        ];
    }
}
