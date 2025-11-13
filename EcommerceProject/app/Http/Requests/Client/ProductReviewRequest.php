<?php

namespace App\Http\Requests\Client;

use App\Repositories\Contracts\ProductReviewRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class ProductReviewRequest extends FormRequest
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
    public function rules(ProductReviewRepositoryInterface $repository): array
    {
        $review = null;
        if(($this->isMethod('put') || $this->isMethod('patch')) && ($id = $this->route('review'))){
            $fillableFields = ['user_id', 'rating', 'content'];
            $review = $repository->first(
                criteria: fn($query) => $query->where('id', $id),
                columns: ['id', 'product_id', ...$fillableFields],
                throwNotFound: false
            );

            $review && $this->merge(array_merge($review->toArray(), $this->only([...$fillableFields])));
        }

        return [
            'user_id' => 'required|integer|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'The user is required.',
            'user_id.integer' => 'The user ID must be a valid number.',
            'user_id.exists' => 'The selected user does not exist.',
            'rating.required' => 'The rating is required.',
            'rating.integer' => 'The rating must be a valid number.',
            'rating.min' => 'The rating must be at least 1 star.',
            'rating.max' => 'The rating must not exceed 5 stars.',
            'content.string' => 'The review content must be a valid text.',
            'content.max' => 'The review content must not exceed 500 characters.',
        ];
    }
}
