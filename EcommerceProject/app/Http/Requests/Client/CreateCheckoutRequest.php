<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class CreateCheckoutRequest extends FormRequest
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
            'sku' => 'required|string|max:100|exists:product_variants,sku',
            'quantity' => 'required|integer|min:1',
        ];

        if($this->has('cart_items')) {
            if($this->input('cart_items') === 'all') {
                $rules = [
                    'cart_items' => 'required|string|in:all'
                ];

            }else {
                $cartItemIds = is_array($this->input('cart_items')) ? $this->input('cart_items') : explode(',', $this->input('cart_items'));
                $this->merge([
                    'cart_items' => $cartItemIds
                ]);

                $rules = [
                    'cart_items' => 'required|array',
                    'cart_items.*' => 'required|integer|exists:cart_items,id',
                ];
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'sku.required' => 'The SKU is required.',
            'sku.string' => 'The SKU must be a valid text.',
            'sku.max' => 'The SKU must not exceed 100 characters.',
            'sku.exists' => 'The selected SKU does not exist.',
            'quantity.required' => 'The quantity is required.',
            'quantity.integer' => 'The quantity must be a valid number.',
            'quantity.min' => 'The quantity must be at least 1.',

            'cart_items.required' => 'The cart items are required.',
            'cart_items.string' => 'The cart items must be a valid text.',
            'cart_items.in' => 'The cart items must be "all" or a valid list of item IDs.',
            'cart_items.array' => 'The cart items must be a valid list.',
            'cart_items.*.required' => 'Each cart item is required.',
            'cart_items.*.integer' => 'Each cart item must be a valid number.',
            'cart_items.*.exists' => 'One or more selected cart items do not exist.'
        ];
    }
}
