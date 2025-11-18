<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BaseApiController extends Controller
{
    const INVALID_ID = -1;

    protected int $defaultPerPage = 20;
    protected int $maxPerPage = 50;

    protected function getPerPage(Request $request): int
    {
        return min($request->integer('per_page', $this->defaultPerPage), $this->maxPerPage);
    }

    protected function response(bool $success, string $message, int $code = 200, array $data = [], mixed $additionalData = null): JsonResponse
    {
        $responseData = [
            'success'=> $success,
            'message'=> $message,
        ];

        if(!empty($data)) $responseData['data'] = $data;
        if(!is_null($additionalData)){
            if(is_array($additionalData)) $responseData = array_merge($responseData, $additionalData);
            else $responseData['additional_data'] = $additionalData;
        }

        return response()->json($responseData, $code);
    }

    protected function authorizeRole(array|UserRole $roles = UserRole::ADMIN): bool
    {
        $userRole = UserRole::tryFrom(authPayload('role'));

        return in_array($userRole, is_array($roles) ? $roles : [$roles], true);
    }

    protected function forbiddenResponse(string $message = 'You do not have permission to perform this action.'): JsonResponse
    {
        return $this->response(
            success: false,
            message: $message,
            code: 403,
        );
    }
}
