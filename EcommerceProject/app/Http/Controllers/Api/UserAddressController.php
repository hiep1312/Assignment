<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Client\UserAddressRequest;
use App\Repositories\Contracts\UserAddressRepositoryInterface;
use Illuminate\Http\Request;

class UserAddressController extends BaseApiController
{
    const API_FIELDS = ['id', 'user_id', 'recipient_name', 'phone', 'province', 'district', 'ward', 'street', 'postal_code', 'is_default', 'created_at'];

    public function __construct(
        protected UserAddressRepositoryInterface $repository
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userAddresses = $this->repository->getAll(
            criteria: function(&$query) use ($request) {
                $query->when(isset($request->search), function($innerQuery) use ($request){
                    $innerQuery->where(function($subQuery) use ($request){
                        $subQuery->whereLike('recipient_name', '%'. trim($request->search) .'%')
                            ->orWhereRaw("CONCAT(province, ', ', district, ', ', ward, ', ', street, ', ', postal_code) LIKE ?", ['%'. trim($request->search) .'%']);
                    });
                })->when(
                    isset($request->is_default),
                    fn($innerQuery) => $innerQuery->where('is_default', $request->is_default)
                )->when(
                    isset($request->phone),
                    fn($innerQuery) => $innerQuery->where('phone', $request->phone)
                );

                $query->where('user_id', authPayload('sub'));
            },
            perPage: $this->getPerPage($request),
            columns: self::API_FIELDS,
            pageName: 'page'
        );

        return $this->response(
            success: true,
            message: 'User address list retrieved successfully.',
            additionalData: $userAddresses->withQueryString()->toArray()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserAddressRequest $request)
    {
        $validatedData = $request->validated();
        $createdUserAddress = $this->repository->create(
            $validatedData + ['user_id' => authPayload('sub')]
        );

        return $this->response(
            success: true,
            message: 'User address created successfully.',
            code: 201,
            data: $createdUserAddress->only(self::API_FIELDS)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $userAddress = $this->repository->first(
            criteria: fn($query) => $query->where('id', $id)
                ->where('user_id', authPayload('sub')),
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return $this->response(
            success: (bool) $userAddress,
            message: $userAddress
                ? 'User address retrieved successfully.'
                : 'User address not found.',
            code: $userAddress ? 200 : 404,
            data: $userAddress?->toArray() ?? []
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserAddressRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $isUpdated = $this->repository->update(
            idOrCriteria: $id,
            attributes: $validatedData,
            updatedModel: $updatedUserAddress
        );

        return $this->response(
            success: (bool) $isUpdated,
            message: $isUpdated
                ? 'User address updated successfully.'
                : 'User address not found.',
            code: $isUpdated ? 200 : 404,
            data: $updatedUserAddress?->only(self::API_FIELDS) ?? []
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $isDeleted = $this->repository->delete(
            idOrCriteria: fn($query) => $query->where('id', $id)
                ->where('user_id', authPayload('sub'))
        );

        return $this->response(
            success: (bool) $isDeleted,
            message: $isDeleted
                ? 'User address deleted successfully.'
                : 'User address not found.',
            code: $isDeleted ? 200 : 404
        );
    }
}
