<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiQueryRelation;
use App\Http\Requests\Client\CartRequest;
use App\Http\Requests\Client\DeleteCartItemsRequest;
use App\Repositories\Contracts\CartItemRepositoryInterface;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends BaseApiController
{
    use ApiQueryRelation;

    const API_FIELDS = ['id', 'user_id', 'guest_token', 'status', 'expires_at', 'created_at'];

    protected function getAllowedRelationsWithFields(): array
    {
        return [
            'user' => UserController::API_FIELDS,
            'items' => (object)[
                'fields' => CartItemController::API_FIELDS,
                'productVariant' => (object)[
                    'fields' => ProductVariantController::API_FIELDS,
                    'inventory' => ProductVariantController::INVENTORY_FIELDS,
                    'product' => ProductController::API_FIELDS
                ],
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
        protected CartRepositoryInterface $repository,
        protected CartItemRepositoryInterface $cartItemRepository,
        protected CartService $service
    ){}

    /**
     * Store a newly created resource in storage.
     */
    public function store(CartRequest $request)
    {
        $validatedData = $request->validated();
        $creationResult = $this->service->create($validatedData);

        return $this->response(
            success: $creationResult['success'],
            message: $creationResult['message'],
            code: $creationResult['code'],
            data: $creationResult['data']?->only(['items', ...self::API_FIELDS]) ?? [],
            additionalData: isset($creationResult['insufficient_stock_skus'])
                ? ['insufficient_stock_skus' => $creationResult['insufficient_stock_skus']]
                : null
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $cart = $this->repository->first(
            criteria: function($query) use ($request, $id){
                $this->getRequestedAggregateRelations($request, $query)
                    ->with($this->getRequestedRelations($request))
                    ->where('id', $id);

                $query->when(...CartService::userQueryConditions());
            },
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return $this->response(
            success: (bool) $cart,
            message: $cart
                ? 'Cart retrieved successfully.'
                : 'Cart not found.',
            code: $cart ? 200 : 404,
            data: $cart?->toArray() ?? []
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CartRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $updationResult = $this->service->update($validatedData, $id);

        return $this->response(
            success: $updationResult['success'],
            message: $updationResult['message'],
            code: $updationResult['success'] ? 200 : 422,
            data: $updationResult['data']?->only(['items', ...self::API_FIELDS]) ?? [],
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $isDeleted = $this->repository->delete(
            idOrCriteria: function($query) use ($id){
                $query->where('id', $id)
                    ->when(...CartService::userQueryConditions());
            }
        );

        return $this->response(
            success: (bool) $isDeleted,
            message: $isDeleted
                ? 'Cart deleted successfully.'
                : 'Cart not found.',
            code: $isDeleted ? 200 : 404
        );
    }

    public function deleteItems(DeleteCartItemsRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $isDeleted = $this->cartItemRepository->delete(
            idOrCriteria: function($query) use ($id, $validatedData){
                $query->whereIn('id', $validatedData['item_ids'])
                    ->where('cart_id', $id)
                    ->whereHas('cart', function($subQuery) {
                        $subQuery->when(...CartService::userQueryConditions());
                    });
            },
        );

        return $this->response(
            success: (bool) $isDeleted,
            message: $isDeleted
                ? 'Cart items deleted successfully.'
                : 'No matching cart items found or unauthorized access.',
            code: $isDeleted ? 200 : 404,
            data: [
                'deleted_count' => (int) $isDeleted,
                'requested_item_ids' => $validatedData['item_ids']
            ]
        );
    }

    public function refresh(Request $request)
    {
        if(!(Auth::guard('jwt')->check() || request()->cookie('cart_guest'))) {
            return $this->response(
                success: false,
                message: 'Unauthorized: No valid user or guest token found.',
                code: 401
            );
        }

        $this->repository->refreshAndCleanupCarts(5, 'DAY');
        $cart = $this->repository->first(
            criteria: function($query) use ($request){
                $this->getRequestedAggregateRelations($request, $query)
                    ->with($this->getRequestedRelations($request));

                $query->when(...CartService::userQueryConditions());
            },
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return $this->response(
            success: (bool) $cart,
            message: $cart
                ? 'Cart refreshed successfully.'
                : 'Cart not found or expired.',
            code: $cart ? 200 : 404,
            data: $cart?->toArray() ?? []
        );
    }
}
