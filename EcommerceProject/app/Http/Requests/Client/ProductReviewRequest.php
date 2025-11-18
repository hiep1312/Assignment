<?php

namespace App\Http\Requests\Client;

use App\Helpers\RequestUtilities;
use App\Repositories\Contracts\ProductReviewRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;


class ProductReviewRequest extends FormRequest
{
    use RequestUtilities;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function getFillableFields(): array
    {
        return ['rating', 'content'];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $requirementRule = $this->isUpdate('review') ? 'sometimes' : 'required';

        return [
            'rating' => "{$requirementRule}|integer|min:1|max:5",
            'content' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'rating.required' => 'The rating is required.',
            'rating.integer' => 'The rating must be a valid number.',
            'rating.min' => 'The rating must be at least 1 star.',
            'rating.max' => 'The rating must not exceed 5 stars.',
            'content.string' => 'The review content must be a valid text.',
            'content.max' => 'The review content must not exceed 500 characters.',
        ];
    }
}
