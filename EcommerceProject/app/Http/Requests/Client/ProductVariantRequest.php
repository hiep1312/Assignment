<?php

namespace App\Http\Requests\Client;

use App\Helpers\RequestUtilities;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductVariantRequest extends FormRequest
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
        $sometimesRule = $this->isUpdate('variant') ? 'sometimes|' : '';
        $VariantSku = $this->route('variant');
        $rules = [
            'name' => $sometimesRule . 'required|string|max:255',
            'sku' => [str_replace('|', '', $sometimesRule), 'required', 'string', 'max:100', Rule::unique('product_variants')->ignore($VariantSku, 'sku')],
            'price' => $sometimesRule . 'required|integer|min:0',
            'discount' => $sometimesRule . 'nullable|integer|min:0|lte:price',
            'status' => $sometimesRule . 'required|integer|in:0,1',
            'stock' => $sometimesRule . 'required|integer|min:0',
            'reserved' => $sometimesRule . 'nullable|integer|min:0|lte:stock',
            'sold_number' => $sometimesRule . 'nullable|integer|min:0',
        ];

        if(!$this->isUpdate('variant')){
            unset($rules['reserved'], $rules['sold_number']);
        }

        return $rules;
    }

    public function messages()
    {
        return [
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
            'reserved.integer' => 'The reserved quantity must be a valid number.',
            'reserved.min' => 'The reserved quantity must be at least 0.',
            'reserved.lte' => 'The reserved quantity cannot exceed the stock quantity.',
            'sold_number.integer' => 'The sold number must be a valid number.',
            'sold_number.min' => 'The sold number must be at least 0.',
        ];
    }
}
