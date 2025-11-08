<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MailCenterRequest extends FormRequest
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
    public function rules(string $sendType): array
    {
        $rules = [
            'selectedTemplate' => 'required|integer|exists:mails,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'selectedUsers' => 'required|array|min:1',
            'selectedUsers.*' => 'integer|exists:users,id',
        ];

        if($sendType === 'template') {
            unset($rules['subject'], $rules['body']);
        }else {
            unset($rules['selectedTemplate']);
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'selectedTemplate.required' => 'Please select an email template.',
            'selectedTemplate.integer' => 'The selected template must be a valid number.',
            'selectedTemplate.exists' => 'The selected template does not exist in the system.',
            'subject.required' => 'The email subject is required.',
            'subject.string' => 'The subject must be a valid text string.',
            'subject.max' => 'The subject must not exceed 255 characters.',
            'body.required' => 'The email body is required.',
            'body.string' => 'The email body must be a valid text string.',
            'selectedUsers.required' => 'Please select at least one user to send the email.',
            'selectedUsers.array' => 'The selected users must be a valid list.',
            'selectedUsers.min' => 'Please select at least one user to send the email.',
            'selectedUsers.*.integer' => 'Each selected user must be a valid number.',
            'selectedUsers.*.exists' => 'One or more selected users do not exist in the system.',
        ];
    }
}
