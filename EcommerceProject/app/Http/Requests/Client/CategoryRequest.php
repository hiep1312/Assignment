<?php

namespace App\Http\Requests\Client;

use App\Helpers\RequestUtilities;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

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

    protected function getFillableFields()
    {
        return ['name', 'slug'];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(CategoryRepositoryInterface $repository): array
    {
        $category = null;
        if($this->isUpdate('category')){
            $category = $repository->first(
                criteria: fn($query) => $query->where('slug', $this->route('category')),
                columns: ['id', ...$this->getFillableFields()],
                throwNotFound: false
            );

            $this->fillMissingWithExisting(
                $category,
                dataOld: $category?->toArray(),
                dataNew: $this->only($this->getFillableFields())
            );
        }

        return [
            'name' => 'required|string|max:255|unique:categories,name' . ($category ? ",{$category->id}" : ''),
            'slug' => 'required|string|max:255|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/|unique:categories,slug' . ($category ? ",{$category->id}" : ''),
        ];
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
