<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiQueryRelationHelper;
use App\Repositories\Contracts\UserAddressRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAddressController extends BaseApiController
{
    use ApiQueryRelationHelper;

    const PRIVATE_FIELDS = ['id', 'user_id', 'recipient_name', 'phone', 'province', 'district', 'ward', 'street', 'postal_code', 'is_default', 'created_at'];

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

                $query->where('user_id', Auth::guard('jwt')->payload()->get('sub'));
            },
            perPage: $this->getPerPage($request),
            columns: self::PRIVATE_FIELDS,
            pageName: 'page'
        );

        return $this->response(
            success: true,
            message: 'User address list retrieved successfully.',
            additionalData: $userAddresses->toArray()
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
