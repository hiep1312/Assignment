<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiQueryRelationHelper;
use App\Http\Requests\Client\UserRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseApiController
{
    use ApiQueryRelationHelper;

    const API_FIELDS = ['id', 'first_name', 'last_name', 'birthday', 'avatar', 'role'];
    const PRIVATE_FIELDS = [...self::API_FIELDS, 'email', 'username', 'created_at', 'email_verified_at'];

    public function getAllowedRelationsWithFields(): array
    {
        return [
            'addresses' => UserAddressController::PRIVATE_FIELDS
        ];
    }

    public function __construct(
        protected UserRepositoryInterface $repository
    ){}

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $user = $this->repository->first(
            criteria: function($query) use ($request){
                $query->with($this->getRequestedRelations($request))
                    ->where('id', Auth::guard('jwt')->payload()->get('sub'));
            },
            columns: self::PRIVATE_FIELDS,
            throwNotFound: false
        );

        return $this->response(
            success: (bool) $user,
            message: 'Profile retrieved successfully.',
            code: 200,
            data: $user->toArray()
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request)
    {
        $validatedData = $request->validated();
        $isUpdated = $this->repository->update(
            idOrCriteria: $request->id,
            attributes: $validatedData,
            updatedModel: $updatedUser
        );

        return $this->response(
            success: (bool) $isUpdated,
            message: 'Profile updated successfully.',
            code: 200,
            data: $updatedUser->only(self::PRIVATE_FIELDS),
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $isDeleted = $request->user('jwt')->delete();

        return $this->response(
            success: (bool) $isDeleted,
            message: 'Profile deleted successfully.',
            code: 200,
        );
    }
}
