<?php

namespace App\Http\Requests\Client;

use App\Helpers\RequestUtilities;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
        $categorySlug = $this->route('category');

        return $this->applyUpdateRules([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($categorySlug, 'slug')],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('categories')->ignore($categorySlug, 'slug')],
        ], 'category');
    }

    public function messages()
    {
        return [
            'name.required' => 'The category name field is required.',
            'name.string' => 'The category name must be a string.',
            'name.max' => 'The category name must not exceed 255 characters.',
            'name.unique' => 'This category name has already been taken.',
            'slug.required' => 'The slug field is required.',
            'slug.string' => 'The slug must be a string.',
            'slug.max' => 'The slug must not exceed 255 characters.',
            'slug.regex' => 'The slug must only contain lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'This slug has already been taken.',
        ];
    }
}
