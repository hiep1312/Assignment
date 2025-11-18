<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiQueryRelation;
use App\Http\Requests\Client\OrderItemRequest;
use App\Repositories\Contracts\OrderItemRepositoryInterface;
use App\Services\OrderItemService;
use Illuminate\Http\Request;

class OrderItemController extends BaseApiController
{
    use ApiQueryRelation;

    const API_FIELDS = ['id', 'order_id', 'product_variant_id', 'quantity', 'price', 'created_at'];

    protected function getAllowedRelationsWithFields(): array
    {
        return [
            'productVariant' => (object)[
                'fields' => ProductVariantController::API_FIELDS,
                'inventory' => ProductVariantController::INVENTORY_FIELDS,
                'product' => ProductController::API_FIELDS
            ]
        ];
    }

    public function __construct(
        protected OrderItemRepositoryInterface $repository,
        protected OrderItemService $service,
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $orderCode)
    {
        $orderItems = $this->repository->getAll(
            criteria: function(&$query) use ($request, $orderCode) {
                $query->with($this->getRequestedRelations($request));

                $query->when(isset($request->search), function($innerQuery) use ($request){
                    $innerQuery->whereHas('productVariant', function($subQuery) use ($request){
                        $subQuery->whereRaw("CONCAT((SELECT title FROM products WHERE products.id = product_variants.product_id), ' ', name) LIKE ?", ['%'. trim($request->search) .'%']);
                    });
                })->when(
                    isset($request->price_range),
                    function($innerQuery) use ($request){
                        $priceRange = is_array($request->price_range) ? $request->price_range : preg_split('/\s*-\s*/', $request->price_range, 2);
                        $minPrice = is_numeric($priceRange[0]) ? (int) $priceRange[0] : 0;
                        $maxPrice = is_numeric($priceRange[1] ?? null) ? (int) $priceRange[1] : PHP_INT_MAX;

                        $innerQuery->whereRaw('price * quantity BETWEEN ? AND ?', [$minPrice, $maxPrice]);
                    }
                );

                $query->whereHas(
                    'order',
                    function($subQuery) use ($orderCode){
                        $subQuery->where('order_code', $orderCode)
                            ->where('user_id', authPayload('sub'));
                    }
                );
            },
            perPage: $this->getPerPage($request),
            columns: self::API_FIELDS,
            pageName: 'page'
        );

        return $this->response(
            success: true,
            message: 'Order item list retrieved successfully.',
            additionalData: $orderItems->toArray()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderItemRequest $request, string $orderCode)
    {
        $validatedData = $request->validated();
        $preparedItem = $this->service->prepareOrderItem($validatedData['sku'], $validatedData['quantity']);

        if($preparedItem){
            $isCreated = $this->repository->createByOrderCode(
                attributes: $preparedItem,
                orderCode: $orderCode,
                createdModel: $createdOrderItem
            );
        }else {
            return $this->response(
                success: false,
                message: 'The requested quantity exceeds the available stock for this product.',
                code: 422,
            );
        }

        return $this->response(
            success: (bool) $isCreated,
            message: $isCreated
                ? 'Order item created successfully.'
                : 'Failed to create order item.',
            code: $isCreated ? 201 : 400,
            data: $createdOrderItem?->only(self::API_FIELDS) ?? []
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $orderCode, string $id)
    {
        $orderItem = $this->repository->first(
            criteria: function($query) use ($request, $id, $orderCode){
                $query->with($this->getRequestedRelations($request))
                    ->where('id', $id)
                    ->whereHas('order', function($subQuery) use ($orderCode){
                        $subQuery->where('order_code', $orderCode)
                            ->where('user_id', authPayload('sub'));
                    });
            },
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return $this->response(
            success: (bool) $orderItem,
            message: $orderItem
                ? 'Order item retrieved successfully.'
                : 'Order item not found.',
            code: $orderItem ? 200 : 404,
            data: $orderItem?->toArray() ?? []
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderItemRequest $request)
    {
        $validatedData = $request->validated();

        if(
            isset($request->id) &&
            ($adjustedData = $this->service->adjustOrderItemQuantity($request->product_variant_id, $request->old_quantity, $validatedData['quantity']))
        ){
            $isUpdated = is_bool($adjustedData)
                ? (bool)($updatedOrderItem = $request)
                : $this->repository->update(
                    idOrCriteria: $request->id,
                    attributes: $adjustedData,
                    updatedModel: $updatedOrderItem
                );
        }else {
            return $this->response(
                success: false,
                message: 'The requested quantity exceeds the available stock for this product.',
                code: 422,
            );
        }

        return $this->response(
            success: (bool) $isUpdated,
            message: $isUpdated
                ? 'Order item updated successfully.'
                : 'Order item not found.',
            code: $isUpdated ? 200 : 404,
            data: $updatedOrderItem?->only(self::API_FIELDS) ?? []
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $orderCode, string $id)
    {
        $isDeleted = $this->repository->delete(
            idOrCriteria: function($query) use ($orderCode, $id){
                $query->where('id', $id)
                    ->whereHas('order', function($subQuery) use ($orderCode){
                        $subQuery->where('order_code', $orderCode)
                            ->where('user_id', authPayload('sub'));
                    });
            }
        );

        return $this->response(
            success: (bool) $isDeleted,
            message: $isDeleted
                ? 'Order item deleted successfully.'
                : 'Order item not found.',
            code: $isDeleted ? 200 : 404
        );
    }
}
