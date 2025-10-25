<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
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
        $rules = [
            'title' => 'nullable|string|max:255',
            'link_url' => 'required|string|url|max:255',
            'image_id' => 'required|integer|exists:images,id',
            'position' => 'required|integer|min:1',
            'status' => 'required|integer|in:1,2',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title must not exceed 255 characters.',
            'link_url.required' => 'The link URL is required.',
            'link_url.string' => 'The link URL must be a string.',
            'link_url.url' => 'The link URL must be a valid URL.',
            'link_url.max' => 'The link URL must not exceed 255 characters.',
            'image_id.required' => 'The image is required.',
            'image_id.integer' => 'The image ID must be an integer.',
            'image_id.exists' => 'The selected image does not exist.',
            'position.required' => 'The position is required.',
            'position.integer' => 'The position must be an integer.',
            'position.min' => 'The position must be at least 1.',
            'status.required' => 'The status is required.',
            'status.integer' => 'The status must be an integer.',
            'status.in' => 'The status must be either active or inactive.',
        ];
    }
}
