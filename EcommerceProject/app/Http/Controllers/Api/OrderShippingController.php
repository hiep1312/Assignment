<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Client\OrderShippingRequest;
use App\Repositories\Contracts\OrderShippingRepositoryInterface;
use App\Services\OrderShippingService;

class OrderShippingController extends BaseApiController
{
    const API_FIELDS = ['id', 'order_id', 'recipient_name', 'phone', 'province', 'district', 'ward', 'street', 'postal_code', 'note', 'created_at'];

    public function __construct(
        protected OrderShippingRepositoryInterface $repository,
        protected OrderShippingService $service
    ){}

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderShippingRequest $request, string $orderCode)
    {
        if(!$this->service->existsWithoutShipping($orderCode)){
            return $this->response(
                success: false,
                message: 'Shipping info already exists for this order.',
                code: 409,
            );
        }

        $validatedData = $request->validated();
        $isCreated = $this->repository->createByOrderCode(
            attributes: $validatedData,
            orderCode: $orderCode,
            createdModel: $createdShipping
        );

        return $this->response(
            success: (bool) $isCreated,
            message: $isCreated
                ? 'Order shipping created successfully.'
                : 'Failed to create order shipping.',
            code: $isCreated ? 201 : 400,
            data: $createdShipping?->only(self::API_FIELDS) ?? []
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $orderCode)
    {
        $shipping = $this->repository->first(
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
            success: (bool) $shipping,
            message: $shipping
                ? 'Order shipping retrieved successfully.'
                : 'Order shipping not found.',
            code: $shipping ? 200 : 404,
            data: $shipping?->toArray() ?? []
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderShippingRequest $request)
    {
        if($request->id && !$request->shipping_updatable){
            return $this->response(
                success: false,
                message: 'Shipping address cannot be updated for this order.',
                code: 403,
            );
        }

        $validatedData = $request->validated();
        $isUpdated = $this->repository->update(
            idOrCriteria: $request->id ?? self::INVALID_ID,
            attributes: $validatedData,
            updatedModel: $updatedShipping
        );

        return $this->response(
            success: (bool) $isUpdated,
            message: $isUpdated
                ? 'Order shipping updated successfully.'
                : 'Order shipping not found.',
            code: $isUpdated ? 200 : 404,
            data: $updatedShipping?->only(self::API_FIELDS) ?? []
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $orderCode)
    {
        $isDeleted = $this->repository->delete(
            idOrCriteria: function($query) use ($orderCode){
                $query->whereHas('order', function($subQuery) use ($orderCode){
                        $subQuery->where('order_code', $orderCode)
                            ->where('user_id', authPayload('sub'));
                    });
            }
        );

        return $this->response(
            success: (bool) $isDeleted,
            message: $isDeleted
                ? 'Order shipping deleted successfully.'
                : 'Order shipping not found.',
            code: $isDeleted ? 200 : 404
        );
    }
}
