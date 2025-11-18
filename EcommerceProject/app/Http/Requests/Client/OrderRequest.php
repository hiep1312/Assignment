<?php

namespace App\Http\Requests\Client;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Helpers\RequestUtilities;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

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

    protected function getFillableFields(): array
    {
        return ['order_code', 'total_amount', 'shipping_fee', 'status', 'customer_note', 'admin_note', 'cancel_reason', 'confirmed_at', 'processing_at', 'shipped_at', 'delivered_at', 'completed_at', 'cancelled_at', 'created_at'];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(OrderRepositoryInterface $repository): array
    {
        $rules = [
            'order_code' => 'required|string|max:100|unique:orders,order_code',
            'total_amount' => 'required|integer|min:0',
            'shipping_fee' => 'nullable|integer|min:0',
            'status' => 'nullable|integer|in:1',
            'customer_note' => 'nullable|string|max:500',
            'admin_note' => 'nullable|string|max:500',
            'cancel_reason' => 'nullable|string|max:255',
            'confirmed_at' => 'nullable|datetime|after_or_equal:created_at',
            'processing_at' => 'nullable|datetime|after_or_equal:confirmed_at',
            'shipped_at' => 'nullable|datetime|after_or_equal:processing_at',
            'delivered_at' => 'nullable|datetime|after_or_equal:shipped_at',
            'completed_at' => 'nullable|datetime|after_or_equal:delivered_at',
            'cancelled_at' => 'nullable|datetime|after_or_equal:created_at',
            'created_at' => 'nullable|datetime',
        ];

        if($this->isUpdate('order')){
            unset($rules['order_code'], $this['order_code']);
            $rules['status'] .= ',2,3,4,5,6,7,8,9';

            $order = $this->processStatusTransition($repository);

            $this->fillMissingWithExisting(
                $order,
                dataOld: $order?->toArray(),
                dataNew: $this->only($this->getFillableFields())
            );
        }else{
            $rules = Arr::only($rules, ['order_code', 'total_amount', 'shipping_fee', 'status', 'customer_note']);
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
            'confirmed_at.datetime' => 'The confirmation date must be a valid datetime.',
            'confirmed_at.after_or_equal' => 'The confirmation date must be on or after the order creation date.',
            'processing_at.datetime' => 'The processing date must be a valid datetime.',
            'processing_at.after_or_equal' => 'The processing date must be on or after the confirmation date.',
            'shipped_at.datetime' => 'The shipping date must be a valid datetime.',
            'shipped_at.after_or_equal' => 'The shipping date must be on or after the processing date.',
            'delivered_at.datetime' => 'The delivery date must be a valid datetime.',
            'delivered_at.after_or_equal' => 'The delivery date must be on or after the shipping date.',
            'completed_at.datetime' => 'The completion date must be a valid datetime.',
            'completed_at.after_or_equal' => 'The completion date must be on or after the delivery date.',
            'cancelled_at.datetime' => 'The cancellation date must be a valid datetime.',
            'cancelled_at.after_or_equal' => 'The cancellation date must be on or after the order creation date.',
            'created_at.datetime' => 'The creation date must be a valid datetime.',
        ];
    }

    protected function processStatusTransition(OrderRepositoryInterface $repository): ?Order
    {
        if(!(
            $this->isUpdate('order') &&
            ($newStatus = OrderStatus::tryFrom($this->route('order')))
        )) return null;

        ['role' => $role, 'sub' => $userId] = authPayload();
        $order = $repository->first(
            criteria: fn($query) => $query->where('order_code', $this->route('order'))
                ->when($role === UserRole::USER->value, fn($query) => $query->where('user_id', $userId)),
            columns: ['id', 'user_id', ...$this->getFillableFields()],
            throwNotFound: false
        );

        if($order && !$order->isCancelled && !$order->isFinalized){
            $currentStatus = OrderStatus::tryFrom($order?->status ?? -1);
            $cancelStatus = $role === UserRole::ADMIN->value ? OrderStatus::ADMIN_CANCEL : OrderStatus::BUYER_CANCEL;

            $allowedTransitions = match($currentStatus){
                OrderStatus::NEW => [OrderStatus::CONFIRMED, $cancelStatus],
                OrderStatus::CONFIRMED => [OrderStatus::PROCESSING, $cancelStatus],
                OrderStatus::PROCESSING => [OrderStatus::SHIPPED, $cancelStatus],
                OrderStatus::SHIPPED => [OrderStatus::DELIVERED, $cancelStatus],
                OrderStatus::DELIVERED => [OrderStatus::COMPLETED, OrderStatus::FAILED],
                default => []
            };

            if(in_array($newStatus, $allowedTransitions, true)){
                $timestampFields = ['confirmed_at', 'processing_at', 'shipped_at', 'delivered_at', 'completed_at', 'cancelled_at', 'created_at'];
                $existingTimestamps = $order->only($timestampFields);
                $isCancellationStatus = in_array($newStatus, [OrderStatus::FAILED, OrderStatus::BUYER_CANCEL, OrderStatus::ADMIN_CANCEL], true);

                $this->merge(array_merge(
                    $existingTimestamps,
                    [$newStatus->timestampColumn() => now()],
                    [
                        'cancel_reason' => $isCancellationStatus ? $this->input('cancel_reason') : null,
                        'customer_note' => $order->allowCustomerNote() ? $this->input('customer_note') : null,
                        'admin_note' => ($role === UserRole::ADMIN->value && $order->allowAdminNote()) ? $this->input('admin_note') : null
                    ]
                ));

                return $order;
            }
        }

        return null;
    }
}
