<?php

namespace App\Http\Requests\Client;

use App\Helpers\RequestUtilities;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'status' => 'sometimes|nullable|integer|in:1,2,3,4,5,6,7,8,9',
            'customer_note' => 'sometimes|nullable|string|max:500',
            'admin_note' => 'sometimes|nullable|string|max:500',
            'cancel_reason' => 'sometimes|nullable|string|max:255'
        ];

        return $this->applyUpdateRules($rules);
    }

    public function messages()
    {
        return [
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
