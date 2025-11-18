<?php

namespace App\Http\Requests\Client;

use App\Helpers\RequestUtilities;
use App\Repositories\Contracts\OrderItemRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class OrderItemRequest extends FormRequest
{
    use RequestUtilities;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function getFillableFields(): array
    {
        return ['quantity'];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(OrderItemRepositoryInterface $repository): array
    {
        $rules = [
            'sku' => 'required|string|max:100|exists:product_variants,sku',
            'quantity' => 'required|integer|min:1',
        ];

        if($this->isUpdate('item')){
            unset($rules['sku']);

            $orderItem = $repository->first(
                criteria: function($query){
                    $query->where('id', $this->route('item'))
                        ->whereHas('order', function($subQuery){
                            $subQuery->where('order_code', $this->route('order'))
                                ->where('user_id', authPayload('sub'));
                        });
                },
                columns: ['id', 'order_id', 'product_variant_id', 'price', 'created_at', ...$this->getFillableFields()],
                throwNotFound: false
            );

            $this->fillMissingWithExisting(
                $orderItem,
                dataOld: array_merge(
                    $orderItem?->toArray() ?? [],
                    $orderItem ? ['old_quantity' => $orderItem->quantity] : []
                ),
                dataNew: $this->only($this->getFillableFields())
            );
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
        ];
    }
}
