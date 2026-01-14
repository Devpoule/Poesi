<?php

namespace App\Http\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Factory responsible for building a consistent JSON API response structure.
 */
class ApiResponseFactory
{
    public static function success(
        mixed $data = null,
        ?string $message = null,
        string $code = 'SUCCESS',
        ?array $meta = null,
        int $httpStatus = Response::HTTP_OK
    ): JsonResponse {
        return new JsonResponse(
            [
                'status'  => true,
                'type'    => 'success',
                'code'    => $code,
                'message' => $message,
                'data'    => $data,
                'errors'  => null,
                'meta'    => $meta,
            ],
            $httpStatus
        );
    }

    public static function error(
        string $message,
        string $code = 'ERROR',
        string $type = 'error',
        ?array $errors = null,
        mixed $data = null,
        int $httpStatus = Response::HTTP_BAD_REQUEST
    ): JsonResponse {
        return new JsonResponse(
            [
                'status'  => false,
                'type'    => $type,
                'code'    => $code,
                'message' => $message,
                'data'    => $data,
                'errors'  => $errors,
                'meta'    => null,
            ],
            $httpStatus
        );
    }

    public static function validationError(
        string $message,
        ?array $errors = null,
        string $code = 'INVALID_PAYLOAD'
    ): JsonResponse {
        return ApiResponseFactory::error(
            message: $message,
            code: $code,
            type: 'validation_error',
            errors: $errors,
            data: null,
            httpStatus: Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    public static function notFound(
        string $message,
        string $code = 'RESOURCE_NOT_FOUND'
    ): JsonResponse {
        return ApiResponseFactory::error(
            message: $message,
            code: $code,
            type: 'error',
            errors: null,
            data: null,
            httpStatus: Response::HTTP_NOT_FOUND
        );
    }
}
