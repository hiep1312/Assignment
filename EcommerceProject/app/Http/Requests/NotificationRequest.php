<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|integer|in:0,1,2,3,4,5',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'The notification title is required.',
            'title.string' => 'The notification title must be a valid text string.',
            'title.max' => 'The notification title must not exceed 255 characters.',
            'message.required' => 'The notification message is required.',
            'message.string' => 'The notification message must be a valid text string.',
            'type.required' => 'The notification type is required.',
            'type.integer' => 'The notification type must be a valid integer.',
            'type.in' => 'The notification type must be one of the following: 0 (Custom), 1 (Order Update), 2 (Payment Update), 3 (Promotion), 4 (Account Update), 5 (System).',
        ];
    }
}
