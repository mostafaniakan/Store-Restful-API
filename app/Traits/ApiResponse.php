<?php

namespace App\Traits;

trait ApiResponse
{
    protected function successResponse($message = null, $data, $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function errorResponse($message = null, $code)
    {
        return response()->json([
            'status' => 'error',
            'message', $message,
            'data' => null
        ], $code);
    }
}