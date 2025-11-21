<?php

namespace App\Http\Requests\Client;

use App\Helpers\RequestUtilities;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
        $slug = $this->route('product');
        $preparedData = [];

        if($this->input('images')){
            $preparedData['images'] = is_array($this->input('images'))
            ? $this->input('images')
            : explode(',', $this->input('images'));
        }

        if($this->input('categories')){
            $preparedData['categories'] = is_array($this->input('categories'))
                ? $this->input('categories')
                : explode(',', $this->input('categories'));
        }

        $this->merge($preparedData);

        return $this->applyUpdateRules([
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('products')->ignore($slug, 'slug')],
            'description' => 'nullable|string|max:65535',
            'status' => 'required|integer|in:0,1',
            'main_image' => 'nullable|integer|exists:images,id',
            'images.*' => 'required|integer|exists:images,id',
            'categories.*' => 'required|integer|exists:categories,id',
        ], 'product');
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
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description must not exceed 65535 characters.',
            'status.required' => 'The status field is required.',
            'status.integer' => 'The status must be an integer.',
            'status.in' => 'The status must be either Inactive or Active.',
            'main_image.integer' => 'The main image must be a valid ID.',
            'main_image.exists' => 'The selected main image does not exist.',
            'images.*.required' => 'Each image element must have a value.',
            'images.*.integer' => 'Each image must be a valid ID.',
            'images.*.exists' => 'One or more selected images do not exist.',
            'categories.*.required' => 'Each category element must have a value.',
            'categories.*.integer' => 'Each category must be a valid ID.',
            'categories.*.exists' => 'One or more selected categories do not exist.',
        ];
    }
}
