<?php

namespace App\Http\Requests\Client;

use App\Helpers\RequestUtilities;
use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest
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
        return [
            'photo' => 'required|image|max:10240',
        ];
    }

    public function messages()
    {
        return [
            'photo.required' => 'The photo field is required.',
            'photo.image' => 'The file must be an image.',
            'photo.max' => 'The image must not exceed 10MB.',
        ];
    }
}
