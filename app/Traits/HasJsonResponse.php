<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait HasJsonResponse
{
    public function sendSuccess($data = []): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function sendError($errorMessages = [], $code = Response::HTTP_NOT_FOUND): JsonResponse
    {
        $response = [
            'success' => false,
            'data' => $errorMessages,
        ];

        return response()->json($response, $code);
    }
}
