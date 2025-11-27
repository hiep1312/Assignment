<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiQueryRelation;
use App\Http\Requests\Client\CartRequest;
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
            'count' => 'items'
        ];
    }

    public function __construct(
        protected CartRepositoryInterface $repository,
        protected CartService $service
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $carts = $this->repository->getAll(
            criteria: function(&$query) use ($request) {
                $this->getRequestedAggregateRelations($request, $query)
                    ->with($this->getRequestedRelations($request));

                $query->when(
                    isset($request->status),
                    fn($innerQuery) => $innerQuery->where('status', $request->status)
                );

                $query->when(...CartService::userQueryConditions());
            },
            perPage: $this->getPerPage($request),
            columns: self::API_FIELDS,
            pageName: 'page'
        );

        return $this->response(
            success: true,
            message: 'Cart list retrieved successfully.',
            additionalData: $carts->withQueryString()->toArray()
        );
    }

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
            code: $creationResult['success'] ? 201 : 422,
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
}
