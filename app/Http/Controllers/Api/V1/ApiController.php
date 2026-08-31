<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller
{
    protected function success(mixed $data, string $message, int $status = 200, ?array $meta = null): JsonResponse
    {
        $response = ['data' => $data, 'message' => $message, 'errors' => null];

        if ($meta !== null) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $status);
    }
}
