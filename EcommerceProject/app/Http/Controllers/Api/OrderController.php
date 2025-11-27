<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiQueryRelation;
use App\Http\Requests\Client\UpdateOrderRequest;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends BaseApiController
{
    use ApiQueryRelation;

    const API_FIELDS = ['id', 'user_id', 'order_code', 'total_amount', 'shipping_fee', 'status', 'customer_note', 'admin_note', 'cancel_reason', 'confirmed_at', 'processing_at', 'shipped_at', 'delivered_at', 'completed_at', 'cancelled_at', 'created_at'];

    protected function getAllowedRelationsWithFields(): array
    {
        return [
            'user' => UserController::API_FIELDS,
            'items' => (object)[
                'fields' => OrderItemController::API_FIELDS,
                'productVariant' => (object)[
                    'fields' => ProductVariantController::API_FIELDS,
                    'inventory' => ProductVariantController::INVENTORY_FIELDS,
                    'product' => ProductController::API_FIELDS
                ],
            ],
            'shipping' => OrderShippingController::API_FIELDS,
            'payment' => (object)[
                'fields' => PaymentController::API_FIELDS,
                'user' => UserController::API_FIELDS
            ]
        ];
    }

    protected function getAllowedAggregateRelations(): array
    {
        return [
            'count' => 'items',
        ];
    }

    public function __construct(
        protected OrderRepositoryInterface $repository,
        protected OrderService $service
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = $this->repository->getAll(
            criteria: function(&$query) use ($request) {
                $this->getRequestedAggregateRelations($request, $query)
                    ->with($this->getRequestedRelations($request));

                $query->when(isset($request->search), function($innerQuery) use ($request){
                    $innerQuery->where(function($subQuery) use ($request){
                        $subQuery->whereLike('order_code', '%'. trim($request->search) .'%')
                            ->orWhereLike('customer_note', '%'. trim($request->search) .'%')
                            ->orWhereLike('admin_note', '%'. trim($request->search) .'%')
                            ->orWhereLike('cancel_reason', '%'. trim($request->search) .'%');
                    });
                })->when(
                    isset($request->total_range),
                    function($innerQuery) use ($request){
                        $totalRange = is_array($request->total_range) ? $request->total_range : preg_split('/\s*-\s*/', $request->total_range, 2);
                        $minPrice = is_numeric($totalRange[0]) ? (int) $totalRange[0] : 0;
                        $maxPrice = is_numeric($totalRange[1] ?? null) ? (int) $totalRange[1] : PHP_INT_MAX;

                        $innerQuery->whereBetween('total_amount', [$minPrice, $maxPrice]);
                    }
                )->when(
                    isset($request->status),
                    fn($innerQuery) => $innerQuery->where('status', $request->status)
                );

                $query->where('user_id', authPayload('sub'));
            },
            perPage: $this->getPerPage($request),
            columns: self::API_FIELDS,
            pageName: 'page'
        );

        return $this->response(
            success: true,
            message: 'Order list retrieved successfully.',
            additionalData: $orders->withQueryString()->toArray()
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $orderCode)
    {
        $order = $this->repository->first(
            criteria: function($query) use ($request, $orderCode){
                $this->getRequestedAggregateRelations($request, $query)
                    ->with($this->getRequestedRelations($request));

                $query->where('order_code', $orderCode)
                    ->where('user_id', authPayload('sub'));
            },
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return $this->response(
            success: (bool) $order,
            message: $order
                ? 'Order retrieved successfully.'
                : 'Order not found.',
            code: $order ? 200 : 404,
            data: $order?->toArray() ?? []
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, string $orderCode)
    {
        $validatedData = $request->validated();
        $validatedData && ($updationResult = $this->service->update($validatedData, $orderCode));

        if(empty($validatedData)) {
            return $this->response(
                success: false,
                message: 'No valid data provided for update.',
                code: 422,
            );
        }elseif(is_bool($updationResult)) {
            return $this->response(
                success: false,
                message: 'Invalid status transition. Unable to update the order.',
                code: 422,
            );
        }

        [$isUpdated, $updatedOrder] = $updationResult;

        return $this->response(
            success: (bool) $isUpdated,
            message: $isUpdated
                ? 'Order updated successfully.'
                : 'Order not found.',
            code: $isUpdated ? 200 : 404,
            data: $updatedOrder?->only(self::API_FIELDS) ?? []
        );
    }
}
