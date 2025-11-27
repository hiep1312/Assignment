<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\OrderShippingRepositoryInterface;

class OrderShippingController extends BaseApiController
{
    const API_FIELDS = ['id', 'order_id', 'recipient_name', 'phone', 'province', 'district', 'ward', 'street', 'postal_code', 'note', 'created_at'];

    public function __construct(
        protected OrderShippingRepositoryInterface $repository,
    ){}

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
}
