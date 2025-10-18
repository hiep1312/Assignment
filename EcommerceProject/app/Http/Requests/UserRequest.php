<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
    public function rules(bool $isEdit = false, int|string|null $recordId = null, bool $isUploadedFile = false): array
    {
        $uniqueSuffix = ($isEdit && $recordId) ? ",{$recordId}" : '';
        $avatarSuffix = (!$isEdit || $isUploadedFile) ? "|image|max:10240" : '';

        $rules = [
            'email' => 'required|email|max:255|unique:users,email' . $uniqueSuffix,
            'username' => 'required|string|max:70|alpha_dash:ascii|unique:users,username' . $uniqueSuffix,
            'password' => 'required|string|min:8|max:100|confirmed',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'birthday' => 'nullable|date|before:today',
            'avatar' => 'nullable' . $avatarSuffix,
            'role' => ['required', Rule::in(UserRole::cases())],
        ];

        if($isEdit) unset($rules['password']);

        return $rules;
    }

    public function messages()
    {
        return [
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'The email must not exceed 255 characters.',
            'email.unique' => 'This email has already been registered.',
            'username.required' => 'The username field is required.',
            'username.string' => 'The username must be a valid string.',
            'username.max' => 'The username must not exceed 70 characters.',
            'username.alpha_dash' => 'The username may only contain ASCII letters, numbers, dashes, and underscores.',
            'username.unique' => 'This username is already taken.',
            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a valid string.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.max' => 'The password must not exceed 100 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'first_name.required' => 'The first name field is required.',
            'first_name.string' => 'The first name must be a valid string.',
            'first_name.max' => 'The first name must not exceed 100 characters.',
            'last_name.required' => 'The last name field is required.',
            'last_name.string' => 'The last name must be a valid string.',
            'last_name.max' => 'The last name must not exceed 100 characters.',
            'birthday.date' => 'Please enter a valid date.',
            'birthday.before' => 'The birthday must be a date before today.',
            'avatar.image' => 'The avatar must be an image file.',
            'avatar.max' => 'The avatar size must not exceed 10MB.',
            'role.required' => 'The role field is required.',
            'role.in' => 'The selected role is invalid. Must be either admin or user.',
        ];
    }
}
