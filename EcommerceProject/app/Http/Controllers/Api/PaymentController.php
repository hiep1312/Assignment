<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiQueryRelation;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Http\Request;

class PaymentController extends BaseApiController
{
    use ApiQueryRelation;

    const API_FIELDS = ['id', 'order_id', 'user_id', 'method', 'status', 'amount', 'transaction_id', 'transaction_data', 'paid_at', 'created_at'];

    protected function getAllowedRelationsWithFields(): array
    {
        return [
            'user' => UserController::API_FIELDS
        ];
    }

    public function __construct(
        protected PaymentRepositoryInterface $repository
    ){}

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $orderCode)
    {
        $payment = $this->repository->first(
            criteria: function($query) use ($request, $orderCode){
                $query->with($this->getRequestedRelations($request))
                    ->whereHas('order', function($subQuery) use ($orderCode){
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
}
