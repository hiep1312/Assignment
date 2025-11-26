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

        if($this->has('carts')) {
            $cartIds = is_array($this->input('carts')) ? $this->input('carts') : explode(',', $this->input('carts'));
            $this->merge([
                'carts' => $cartIds
            ]);

            $rules = [
                'carts' => 'required|array',
                'carts.*' => 'required|integer|exists:carts,id',
            ];
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

            'carts.required' => 'The cart items are required.',
            'carts.array' => 'The cart items must be a valid list.',
            'carts.*.required' => 'Each cart item is required.',
            'carts.*.integer' => 'Each cart item must be a valid number.',
            'carts.*.exists' => 'One or more selected cart items do not exist.',
        ];
    }
}
