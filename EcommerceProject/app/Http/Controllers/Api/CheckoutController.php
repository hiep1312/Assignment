<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Client\CreateCheckoutRequest;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends BaseApiController
{
    public function __construct(
        protected CheckoutService $service
    ){}

    public function create(CreateCheckoutRequest $request)
    {
        $validatedData = $request->validated();
        $creationResult = $this->service->create($validatedData);

        return $this->response(
            success: $creationResult['success'],
            message: $creationResult['message'],
            code: $creationResult['success'] ? 201 : 422,
            data: $creationResult['data'] ?? []
        );
    }

    public function update(){

    }

    public function cancel(){

    }

    public function finalize(){

    }
}
