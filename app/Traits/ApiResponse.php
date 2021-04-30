<?php

namespace App\Traits;

use \Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    protected function success($data, int $status = Response::HTTP_OK)
    {
        return response()->json([
                    'result' => true,
                    'data' => $data
                ], $status);
    }

    protected function error(string $message, array $errors = [], int $status = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return response()->json([
                    'result' => false,
                    'message' => $message,
                    'data' => $errors
                ], $status);
    }

    protected function validatorError($message = [], array $errors = [])
    {
        return response()->json([
                    'result' => false,
                    'message' => $message,
                    'data' => $errors
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}