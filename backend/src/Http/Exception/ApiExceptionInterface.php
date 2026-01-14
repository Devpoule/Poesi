<?php

namespace App\Http\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * Contract for exceptions that should be converted
 * into a structured JSON API response.
 */
interface ApiExceptionInterface
{
    /**
     * Returns a stable technical error code for the frontend.
     */
    public function getErrorCode(): string;

    /**
     * Returns the HTTP status code associated with the error.
     */
    public function getHttpStatus(): int;

    /**
     * Returns the UI feedback type (success, error, warning, info, etc.).
     */
    public function getType(): string;

    /**
     * Returns the human readable message for the error.
     */
    public function getMessage(): string;
}
