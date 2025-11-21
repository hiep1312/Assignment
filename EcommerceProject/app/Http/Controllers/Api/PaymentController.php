<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends BaseApiController
{
    const API_FIELDS = ['id', 'order_id', 'user_id', 'method', 'status', 'amount', 'transaction_id', 'transaction_data', 'paid_at', 'created_at'];

    public function __construct(
        protected PaymentRepositoryInterface $repository,
        protected PaymentService $service
    ){}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $orderCode)
    {
        $validatedData = $request->validated();
        $creationResult = $this->service->create($validatedData, $orderCode);

        if(is_bool($creationResult)){
            return $this->response(
                success: false,
                message: 'Payment info already exists for this order.',
                code: 409,
            );
        }

        [$isCreated, $createdPayment] = $creationResult;

        return $this->response(
            success: (bool) $isCreated,
            message: $isCreated
                ? 'Payment created successfully.'
                : 'Failed to create payment.',
            code: $isCreated ? 201 : 400,
            data: $createdPayment?->only(self::API_FIELDS) ?? []
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $orderCode)
    {
        $payment = $this->repository->first(
            criteria: function($query) use ($orderCode){
                $query->whereHas('order', function($subQuery) use ($orderCode){
                    $subQuery->where('order_code', $orderCode)
                        ->where('user_id', authPayload('sub'));
                });
            },
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return $this->response(
            success: (bool) $payment,
            message: $payment
                ? 'Order payment retrieved successfully.'
                : 'Order payment not found.',
            code: $payment ? 200 : 404,
            data: $payment?->toArray() ?? []
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
