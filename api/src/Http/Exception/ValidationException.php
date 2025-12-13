<?php

namespace App\Http\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * API validation exception for invalid request payload.
 *
 * Carries field-level errors for client display:
 * - "title" => "Title is required."
 * - "moodColor" => "Invalid mood color."
 */
class ValidationException extends AbstractApiException
{
    /**
     * @var array<string, string>
     */
    private array $errors;

    /**
     * @param string                $message General validation message
     * @param array<string, string> $errors  Field => error message map
     */
    public function __construct(string $message, array $errors = [])
    {
        parent::__construct(
            message: $message,
            errorCode: 'VALIDATION_FAILED',
            httpStatus: Response::HTTP_BAD_REQUEST,
            type: 'validation_error'
        );

        $this->errors = $errors;
    }

    /**
     * Returns field-level validation errors.
     *
     * @return array<string, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
