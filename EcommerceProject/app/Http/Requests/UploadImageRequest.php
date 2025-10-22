<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
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
            'photos.*' => 'image|max:10240',
        ];
    }

    public function messages()
    {
        return [
            'photos.*.image' => 'The file at position :position must be an image.',
            'photos.*.max' => 'The image at position :position must not exceed 10MB.',
        ];
    }
}
