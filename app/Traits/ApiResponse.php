<?php

namespace App\Traits;

trait ApiResponse
{
    protected function successResponse($message = null, $data, $code = 200)
    {
        return response()->json([
            'code' => $code,
            'status' => 'success',
            'message' => $message,
            'data' => $data,

        ], $code);
    }

    protected function errorResponse($message = null, $code)
    {

        return response()->json([
            'code' => $code,
            'status' => 'error',
            'message'=> $message,
            'data' => null,
        ], $code);
    }
}