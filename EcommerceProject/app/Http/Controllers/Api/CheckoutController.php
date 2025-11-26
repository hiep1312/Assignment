<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Client\CreateCheckoutRequest;
use App\Http\Requests\Client\FinalizeCheckoutRequest;
use App\Http\Requests\Client\UpdateCheckoutRequest;
use App\Services\CheckoutService;

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

    public function update(UpdateCheckoutRequest $request, string $orderCode)
    {
        $validatedData = $request->validated();
        if(empty($validatedData)){
            return $this->response(
                success: false,
                message: 'No data provided for update.',
                code: 422,
            );
        }

        $updationResult = $this->service->update($validatedData, $orderCode);

        return $this->response(
            success: $updationResult['success'],
            message: $updationResult['message'],
            code: $updationResult['success'] ? 200 : 422,
            data: $updationResult['data'] ?? []
        );
    }

    public function cancel(string $orderCode)
    {
        $cancelResult = $this->service->cancel($orderCode);

        return $this->response(
            success: $cancelResult['success'],
            message: $cancelResult['message'],
            code: $cancelResult['success'] ? 200 : 422,
            data: $cancelResult['data'] ?? []
        );
    }

    public function finalize(FinalizeCheckoutRequest $request, string $orderCode)
    {
        $validatedData = $request->validated();
        $finalizationResult = $this->service->finalize($validatedData, $orderCode);

        return $this->response(
            success: $finalizationResult['success'],
            message: $finalizationResult['message'],
            code: $finalizationResult['success'] ? 200 : 422,
            data: $finalizationResult['data'] ?? []
        );
    }
}
