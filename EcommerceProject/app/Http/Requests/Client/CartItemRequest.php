<?php

namespace App\Http\Requests\Client;

use App\Helpers\RequestUtilities;
use Illuminate\Foundation\Http\FormRequest;

class CartItemRequest extends FormRequest
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
            'quantity' => 'required|integer|min:1'
        ];

        if($this->isUpdate('item')) {
            $rules['item_id'] = "required|integer|exists:order_items,id";
        }else {
            $rules['sku'] = "required|string|max:100|exists:product_variants,sku";
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'quantity.required' => 'The quantity is required.',
            'quantity.integer' => 'The quantity must be a valid number.',
            'quantity.min' => 'The quantity must be at least 1.',
            'item_id.required' => 'The item ID is required.',
            'item_id.integer' => 'The item ID must be a valid number.',
            'item_id.exists' => 'The selected item ID does not exist.',
            'sku.required' => 'The SKU is required.',
            'sku.string' => 'The SKU must be a valid text.',
            'sku.max' => 'The SKU must not exceed 100 characters.',
            'sku.exists' => 'The selected SKU does not exist.',
        ];
    }
}
