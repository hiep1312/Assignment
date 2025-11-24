<?php

namespace App\Http\Requests\Client;

use App\Helpers\RequestUtilities;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogRequest extends FormRequest
{
    use RequestUtilities;

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
        return $this->applyUpdateRules([
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('blogs')->ignore($this->route('blog'), 'slug')],
            'content' => 'required|string',
            'status' => 'required|integer|in:0,1,2',
            'thumbnail' => 'required|integer|exists:images,id',
            'categories.*' => 'required|integer|exists:categories,id',
        ], 'blog');
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
            'thumbnail.required' => 'The thumbnail is required.',
            'thumbnail.integer' => 'The thumbnail ID must be an integer.',
            'thumbnail.exists' => 'The selected thumbnail does not exist.',
            'categories.*.required' => 'Each category element must have a value.',
            'categories.*.integer' => 'Each category must be a valid ID.',
            'categories.*.exists' => 'One or more selected categories do not exist.',
        ];
    }
}
