<?php

namespace App\Http\Requests\Client;

use App\Helpers\RequestUtilities;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
        $sometimesRule = $this->isUpdate(true) ? 'sometimes|' : '';
        $rules = [
            'order_code' => 'required|string|max:100|unique:orders,order_code',
            'total_amount' => 'required|integer|min:0',
            'shipping_fee' => $sometimesRule . 'nullable|integer|min:0',
            'status' => $sometimesRule . 'nullable|integer|in:1',
            'customer_note' => $sometimesRule . 'nullable|string|max:500',
            'admin_note' => $sometimesRule . 'nullable|string|max:500',
            'cancel_reason' => $sometimesRule . 'nullable|string|max:255'
        ];

        if($this->isUpdate('order')){
            unset($rules['order_code'], $rules['total_amount']);
            $rules['status'] .= ',2,3,4,5,6,7,8,9';
        }else{
            unset($rules['admin_note'], $rules['cancel_reason']);
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'order_code.required' => 'The order code is required.',
            'order_code.string' => 'The order code must be a valid string.',
            'order_code.max' => 'The order code must not exceed 100 characters.',
            'order_code.unique' => 'This order code already exists in the system.',
            'total_amount.required' => 'The total amount is required.',
            'total_amount.integer' => 'The total amount must be a valid number.',
            'total_amount.min' => 'The total amount must be at least 0.',
            'shipping_fee.integer' => 'The shipping fee must be a valid number.',
            'shipping_fee.min' => 'The shipping fee must be at least 0.',
            'status.integer' => 'The status must be a valid number.',
            'status.in' => 'The selected status is invalid.',
            'customer_note.string' => 'The customer note must be a valid text.',
            'customer_note.max' => 'The customer note must not exceed 500 characters.',
            'admin_note.string' => 'The admin note must be a valid text.',
            'admin_note.max' => 'The admin note must not exceed 500 characters.',
            'cancel_reason.string' => 'The cancellation reason must be a valid text.',
            'cancel_reason.max' => 'The cancellation reason must not exceed 255 characters.',
        ];
    }
}
