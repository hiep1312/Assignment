<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class ProductVariantRequest extends FormRequest
{
    public function __construct(
        public string $targetPosition = "variant"
    ){}

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
        $rules = [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:product_variants,sku' . $uniqueSuffix,
            'price' => 'required|integer|min:0',
            'discount' => 'nullable|integer|min:0|lte:price',
            'status' => 'required|integer|in:0,1',
            'stock' => 'required|integer|min:0',
        ];

        return $this->targetPosition === "product" ? Arr::mapWithKeys($rules, fn($value, $key) => ["activeVariantData.{$key}" => $value]) : $rules;
    }

    public function messages()
    {
        $messages = [
            'name.required' => 'The variant name is required.',
            'name.string' => 'The variant name must be a valid text.',
            'name.max' => 'The variant name must not exceed 255 characters.',
            'sku.required' => 'The SKU is required.',
            'sku.string' => 'The SKU must be a valid text.',
            'sku.max' => 'The SKU must not exceed 100 characters.',
            'sku.unique' => 'This SKU already exists. Please use a different SKU.',
            'price.required' => 'The price is required.',
            'price.integer' => 'The price must be a valid number.',
            'price.min' => 'The price must be at least 0.',
            'discount.integer' => 'The discounted price must be a valid number.',
            'discount.min' => 'The discounted price must be at least 0.',
            'discount.lte' => 'The discounted price cannot exceed the original price.',
            'status.required' => 'The status is required.',
            'status.integer' => 'The status must be a valid number.',
            'status.in' => 'The status must be either active or inactive.',
            'stock.required' => 'The stock quantity is required.',
            'stock.integer' => 'The stock quantity must be a valid number.',
            'stock.min' => 'The stock quantity must be at least 0.',
        ];

        return $this->targetPosition === "product" ? Arr::mapWithKeys($messages, fn($value, $key) => ["activeVariantData.{$key}" => $value]) : $messages;
    }
}
