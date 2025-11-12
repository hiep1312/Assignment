<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
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
    public function rules(bool $isEdit = false, int|string|null $recordId = null): array
    {
        $uniqueSuffix = ($isEdit && $recordId) ? ",{$recordId}" : '';

        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/|unique:blogs,slug' . $uniqueSuffix,
            'content' => 'required|string',
            'status' => 'required|integer|in:0,1,2',
            'thumbnail_id' => 'required|integer|exists:images,id'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title must not exceed 255 characters.',
            'slug.required' => 'The slug field is required.',
            'slug.string' => 'The slug must be a string.',
            'slug.max' => 'The slug must not exceed 255 characters.',
            'slug.regex' => 'The slug must only contain lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'This slug has already been taken.',
            'content.required' => 'The content field is required.',
            'content.string' => 'The content must be a string.',
            'status.required' => 'The status field is required.',
            'status.integer' => 'The status must be an integer.',
            'status.in' => 'The status must be Draft (0), Published (1), or Archived (2).',
            'thumbnail_id.required' => 'The thumbnail is required.',
            'thumbnail_id.integer' => 'The thumbnail ID must be an integer.',
            'thumbnail_id.exists' => 'The selected thumbnail does not exist.',
        ];
    }
}
