<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiQueryRelation;
use App\Http\Requests\Client\CartItemRequest;
use App\Repositories\Contracts\CartItemRepositoryInterface;
use App\Services\CartItemService;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartItemController extends BaseApiController
{
    use ApiQueryRelation;

    const API_FIELDS = ['id', 'cart_id', 'product_variant_id', 'quantity', 'price', 'created_at'];

    protected function getAllowedRelationsWithFields(): array
    {
        return [
            'productVariant' => (object)[
                'fields' => ProductVariantController::API_FIELDS,
                'inventory' => ProductVariantController::INVENTORY_FIELDS,
                'product' => ProductController::API_FIELDS
            ],
        ];
    }

    public function __construct(
        protected CartItemRepositoryInterface $repository,
        protected CartItemService $service
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $cartId)
    {
        $cartItems = $this->repository->getAll(
            criteria: function(&$query) use ($request, $cartId) {
                $query->with($this->getRequestedRelations($request));

                $query->when(isset($request->search), function($innerQuery) use ($request){
                    $innerQuery->whereHas('productVariant.product', function($productQuery) use ($request) {
                        $productQuery->whereLike('title', '%'. trim($request->search) .'%');
                    });
                })->when(
                    isset($request->price_range),
                    function($innerQuery) use ($request){
                        $priceRange = is_array($request->price_range) ? $request->price_range : preg_split('/\s*-\s*/', $request->price_range, 2);
                        $minPrice = is_numeric($priceRange[0]) ? (int) $priceRange[0] : 0;
                        $maxPrice = is_numeric($priceRange[1] ?? null) ? (int) $priceRange[1] : PHP_INT_MAX;

                        $innerQuery->whereBetween('price', [$minPrice, $maxPrice]);
                    }
                );

                $query->whereHas(
                    'cart',
                    function($innerQuery) use ($cartId) {
                        $innerQuery->where('id', $cartId)
                            ->when(...CartService::userQueryConditions());
                    }
                );
            },
            perPage: $this->getPerPage($request),
            columns: self::API_FIELDS,
            pageName: 'page'
        );

        return $this->response(
            success: true,
            message: 'Cart items list retrieved successfully.',
            additionalData: $cartItems->withQueryString()->toArray()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CartItemRequest $request, string $cartId)
    {
        $validatedData = $request->validated();
        $creationResult = $this->service->create($validatedData, $cartId);

        return $this->response(
            success: $creationResult['success'],
            message: $creationResult['message'],
            code: $creationResult['success'] ? 201 : 422,
            data: $creationResult['data']?->only(self::API_FIELDS) ?? [],
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $cartItem = $this->repository->first(
            criteria: function($query) use ($request, $id) {
                $query->with($this->getRequestedRelations($request))
                    ->where('id', $id)
                    ->whereHas('cart', function($subQuery){
                        $subQuery->when(...CartService::userQueryConditions());
                    });
            },
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return $this->response(
            success: (bool) $cartItem,
            message: $cartItem
                ? 'Cart Item retrieved successfully.'
                : 'Cart Item not found.',
            code: $cartItem ? 200 : 404,
            data: $cartItem?->toArray() ?? []
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CartItemRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $isUpdated = $this->repository->update(
            idOrCriteria: function ($query) use ($id){
                $query->where('id', $id)
                    ->whereHas('cart', function($subQuery){
                        $subQuery->where('status', 1)
                            ->where('expires_at', '>', now())
                            ->when(...CartService::userQueryConditions());
                    });
            },
            attributes: $validatedData,
            updatedModel: $updatedCartItem
        );
        $updatedCartItem = $updatedCartItem->first();

        return $this->response(
            success: (bool) $isUpdated,
            message: $isUpdated
                ? 'Cart item updated successfully.'
                : 'Cart item not found or not eligible for update.',
            code: $isUpdated ? 200 : 404,
            data: $updatedCartItem?->only(self::API_FIELDS) ?? [],
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
                    ->whereHas('cart', function($subQuery){
                        $subQuery->when(...CartService::userQueryConditions());
                    });
            }
        );

        return $this->response(
            success: (bool) $isDeleted,
            message: $isDeleted
                ? 'Cart item deleted successfully.'
                : 'Cart item not found.',
            code: $isDeleted ? 200 : 404
        );
    }
}
