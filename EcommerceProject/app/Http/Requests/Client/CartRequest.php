<?php

namespace App\Http\Requests\Client;

use App\Helpers\RequestUtilities;
use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
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
        $rules = [
            'cart_items' => 'required|array',
            'cart_items.*.quantity' => 'required|integer|min:1'
        ];

        if($this->isUpdate('cart')) {
            $rules['cart_items.*.item_id'] = "required|integer|exists:order_items,id";
        }else {
            $rules['cart_items.*.sku'] = "required|string|max:100|exists:product_variants,sku";
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'cart_items.required' => 'The cart items are required.',
            'cart_items.array' => 'The cart items must be a valid list.',
            'cart_items.*.item_id.required' => 'The item ID is required for each cart item.',
            'cart_items.*.item_id.integer' => 'The item ID must be a valid number.',
            'cart_items.*.item_id.exists' => 'The selected item ID does not exist.',
            'cart_items.*.sku.required' => 'The SKU is required for each cart item.',
            'cart_items.*.sku.string' => 'The SKU must be a valid text.',
            'cart_items.*.sku.max' => 'The SKU must not exceed 100 characters.',
            'cart_items.*.sku.exists' => 'The selected SKU does not exist.',
            'cart_items.*.quantity.required' => 'The quantity is required for each cart item.',
            'cart_items.*.quantity.integer' => 'The quantity must be a valid number.',
            'cart_items.*.quantity.min' => 'The quantity must be at least 1.',
        ];
    }
}
