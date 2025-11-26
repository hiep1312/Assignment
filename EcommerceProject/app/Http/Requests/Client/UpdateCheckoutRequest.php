<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCheckoutRequest extends FormRequest
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
        return [
            'customer_note' => 'sometimes|nullable|string|max:500',
            'items_update' => 'sometimes|required|array',
            'items_update.*.item_id' => 'required|integer|exists:order_items,id',
            'items_update.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'customer_note.string' => 'The customer note must be a valid text.',
            'customer_note.max' => 'The customer note must not exceed 500 characters.',
            'items_update.required' => 'The items to update are required.',
            'items_update.array' => 'The items to update must be a valid list.',
            'items_update.*.item_id.required' => 'Each item ID is required.',
            'items_update.*.item_id.integer' => 'Each item ID must be a valid number.',
            'items_update.*.item_id.exists' => 'One or more selected items do not exist.',
            'items_update.*.quantity.required' => 'The quantity is required for each item.',
            'items_update.*.quantity.integer' => 'The quantity must be a valid number.',
            'items_update.*.quantity.min' => 'The quantity must be at least 1.',
        ];
    }
}
