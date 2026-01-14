<?php

namespace App\Http\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * Exception thrown when a request payload fails validation.
 *
 * This exception belongs to the HTTP layer.
 * It is meant to be thrown by Request DTOs or controllers
 * when user input is syntactically or semantically invalid.
 *
 * It carries:
 * - a human-readable message
 * - a stable technical error code
 * - a list of field-level validation errors
 */
final class ValidationException extends \RuntimeException implements ApiExceptionInterface
{
    /**
     * Validation errors grouped by field.
     *
     * @var array<string, string[]>
     */
    private array $errors;

    /**
     * Stable technical error code.
     *
     * @var string
     */
    private string $errorCode;

    /**
     * HTTP status code.
     *
     * @var int
     */
    private int $httpStatus;

    /**
     * @param string                 $message
     * @param array<string, string[]> $errors
     * @param string                 $errorCode
     * @param int                    $httpStatus
     */
    private function __construct(
        string $message,
        array $errors,
        string $errorCode,
        int $httpStatus
    ) {
        parent::__construct($message);

        $this->errors = $errors;
        $this->errorCode = $errorCode;
        $this->httpStatus = $httpStatus;
    }

    /**
     * Create a ValidationException from field-level errors.
     *
     * @param string                 $message
     * @param array<string, string[]> $errors
     * @param string                 $errorCode
     * @param int                    $httpStatus
     *
     * @return self
     */
    public static function fromErrors(
        string $message,
        array $errors,
        string $errorCode = 'VALIDATION_ERROR',
        int $httpStatus = Response::HTTP_UNPROCESSABLE_ENTITY
    ): self {
        return new self(
            message: $message,
            errors: $errors,
            errorCode: $errorCode,
            httpStatus: $httpStatus
        );
    }

    /**
     * @return array<string, string[]>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function getType(): string
    {
        return 'error';
    }
}
