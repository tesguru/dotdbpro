<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait JsonResponseTrait
{
    public function successResponse($statusCode = Response::HTTP_OK, $message = 'Successful'): JsonResponse
    {
        $response = [
            'statusCode' => $statusCode,
            'message' => $message,
        ];
        return response()->json($response, $statusCode);
    }

    public function successDataResponse($data, $statusCode = Response::HTTP_OK, $message = 'Successful'): JsonResponse
    {
        $response = [
            'statusCode' => $statusCode,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    public function errorResponse($statusCode = Response::HTTP_BAD_REQUEST, $message = 'Error'): JsonResponse
    {
        return response()->json(
            [
                'statusCode' => $statusCode,
                'message' => $message,
            ],
            $statusCode
        );
    }
}
