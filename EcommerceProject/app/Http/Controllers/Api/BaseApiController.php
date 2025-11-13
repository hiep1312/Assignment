<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class BaseApiController extends Controller
{
    protected int $defaultPerPage = 20;
    protected int $maxPerPage = 50;

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
}
