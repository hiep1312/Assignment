<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'username' => 'required|string',
            'password' => 'required|string|min:8',
            'remember' => 'nullable|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Please enter your username.',
            'username.string' => 'The username is invalid.',
            'password.required' => 'Please enter your password.',
            'password.string' => 'The password is invalid.',
            'password.min' => 'The password must be at least 8 characters long.',
            'remember.boolean' => 'The value of the "Remember me" checkbox is invalid.',
        ];
    }
}
