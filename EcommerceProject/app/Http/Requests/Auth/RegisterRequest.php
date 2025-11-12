<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'email' => 'required|email|max:255|unique:users,email',
            'username' => 'required|string|max:70|alpha_dash:ascii|unique:users,username',
            'password' => 'required|string|min:8|max:100|confirmed',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'birthday' => 'nullable|date|before:today',
            'avatar' => 'nullable|image|max:10240',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'The email address must not exceed 255 characters.',
            'email.unique' => 'This email address is already registered.',
            'username.required' => 'Please enter a username.',
            'username.string' => 'The username must be a valid string.',
            'username.max' => 'The username must not exceed 70 characters.',
            'username.alpha_dash' => 'The username may only contain letters, numbers, dashes, and underscores.',
            'username.unique' => 'This username is already taken.',
            'password.required' => 'Please enter a password.',
            'password.string' => 'The password must be a valid string.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.max' => 'The password must not exceed 100 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'first_name.required' => 'Please enter your first name.',
            'first_name.string' => 'The first name must be a valid string.',
            'first_name.max' => 'The first name must not exceed 100 characters.',
            'last_name.required' => 'Please enter your last name.',
            'last_name.string' => 'The last name must be a valid string.',
            'last_name.max' => 'The last name must not exceed 100 characters.',
            'birthday.date' => 'Please enter a valid date for your birthday.',
            'birthday.before' => 'The birthday must be a date before today.',
            'avatar.image' => 'The avatar must be an image file.',
            'avatar.max' => 'The avatar file size must not exceed 10MB.',
        ];
    }
}
