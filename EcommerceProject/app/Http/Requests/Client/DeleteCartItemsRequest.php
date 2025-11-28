<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class DeleteCartItemsRequest extends FormRequest
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
        $itemIdsInput = $this->input('item_ids');
        if(is_string($itemIdsInput)) {
            $this->merge([
                'item_ids' => preg_split('/\s*,\s*/', (string) $itemIdsInput),
            ]);
        }

        return [
            'item_ids' => 'required|array',
            'item_ids.*' => 'required|integer|exists:order_items,id'
        ];
    }

    public function messages()
    {
        return [
            'item_ids.required' => 'The cart items are required.',
            'item_ids.array' => 'The cart items must be an array.',
            'item_ids.*.required' => 'Each item ID is required.',
            'item_ids.*.integer' => 'Each item ID must be a valid number.',
            'item_ids.*.exists' => 'One or more selected items do not exist in the cart.',
        ];
    }
}
