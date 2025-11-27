<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiQueryRelation;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends BaseApiController
{
    use ApiQueryRelation;

    const API_FIELDS = ['id', 'user_id', 'guest_token', 'status', 'expires_at', 'created_at'];

    protected function getAllowedRelationsWithFields(): array
    {
        return [
            'user' => UserController::API_FIELDS,
            /* 'items' => CartItemController::API_FIELDS */
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

                $query->when(
                    
                );
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
