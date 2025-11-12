<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MailRequest extends FormRequest
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
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
            'type' => 'required|integer|in:0,1,2,3,4,5',
        ];
    }

    public function messages()
    {
        return [
            'subject.string' => 'The subject must be a valid text string.',
            'subject.max' => 'The subject must not exceed 255 characters.',
            'body.required' => 'The email body is required.',
            'body.string' => 'The email body must be a valid text string.',
            'type.required' => 'The email type is required.',
            'type.integer' => 'The email type must be a valid integer.',
            'type.in' => 'The email type must be one of the following: 0 (Custom), 1 (Order Success), 2 (Order Failed), 3 (Shipping Update), 4 (Forgot Password), 5 (Register Success).',
        ];
    }
}
