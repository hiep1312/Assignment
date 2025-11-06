<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogCommentRequest extends FormRequest
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
    public function rules(bool $isEdit = false): array
    {
        $rules = [
            'blog_id' => 'required|integer|exists:blogs,id',
            'content' => 'required|string|max:500',
            'parent_id' => 'nullable|integer|exists:blog_comments,id',
            'reply_to' => 'nullable|integer|exists:blog_comments,id',
        ];

        if($isEdit) unset($rules['blog_id'], $rules['parent_id'], $rules['reply_to']);

        return $rules;
    }

    public function messages()
    {
        return [
            'blog_id.required' => 'The blog field is required.',
            'blog_id.integer' => 'The blog must be an integer.',
            'blog_id.exists' => 'The selected blog does not exist.',
            'content.required' => 'The comment content field is required.',
            'content.string' => 'The comment content must be a string.',
            'content.max' => 'The comment content must not exceed 500 characters.',
            'parent_id.integer' => 'The parent comment must be an integer.',
            'parent_id.exists' => 'The selected parent comment does not exist.',
            'reply_to.integer' => 'The reply to comment must be an integer.',
            'reply_to.exists' => 'The selected reply to comment does not exist.',
        ];
    }
}
