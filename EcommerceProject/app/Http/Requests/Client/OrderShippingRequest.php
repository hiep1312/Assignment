<?php

namespace App\Http\Requests\Client;

use App\Helpers\RequestUtilities;
use App\Models\UserAddress;
use Illuminate\Foundation\Http\FormRequest;

class OrderShippingRequest extends FormRequest
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
        if($this->filled('address_id')) {
            $this->applyAddressTemplate();
        }

        return $this->applyUpdateRules([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|regex:/^(?:[0-9\s\-\+\(\)]*)$/',
            'province' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'ward' => 'required|string|max:100',
            'street' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'note' => 'nullable|string|max:500',
        ], true);
    }

    public function message()
    {
        return [
            'recipient_name.required' => 'Recipient name is required.',
            'recipient_name.string' => 'Recipient name must be a valid text.',
            'recipient_name.max' => 'Recipient name cannot exceed 255 characters.',
            'phone.required' => 'Phone number is required.',
            'phone.string' => 'Phone number must be a valid text.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'phone.regex' => 'Phone number format is invalid. Only numbers, spaces, and characters +, -, (, ) are allowed.',
            'province.required' => 'Province is required.',
            'province.string' => 'Province must be a valid text.',
            'province.max' => 'Province cannot exceed 100 characters.',
            'district.required' => 'District is required.',
            'district.string' => 'District must be a valid text.',
            'district.max' => 'District cannot exceed 100 characters.',
            'ward.required' => 'Ward is required.',
            'ward.string' => 'Ward must be a valid text.',
            'ward.max' => 'Ward cannot exceed 100 characters.',
            'street.string' => 'Street must be a valid text.',
            'street.max' => 'Street cannot exceed 255 characters.',
            'postal_code.string' => 'Postal code must be a valid text.',
            'postal_code.max' => 'Postal code cannot exceed 20 characters.',
            'note.string' => 'Note must be a valid text.',
            'note.max' => 'Note cannot exceed 500 characters.',
        ];
    }

    protected function applyAddressTemplate(): void
    {
        $addressRepository = app()->make(UserAddress::class);
        $addressTemplate = $addressRepository->first(
            criteria: fn($query) => $query->where('id', $this->input('address_id'))
                ->where('user_id', authPayload('sub')),
            columns: ['recipient_name', 'phone', 'province', 'district', 'ward', 'street', 'postal_code', 'note']
        );

        if($addressTemplate){
            $this->merge($addressTemplate->toArray());
        }
    }
}
