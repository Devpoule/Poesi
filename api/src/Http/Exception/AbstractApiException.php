<?php

namespace App\Http\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * Base class for API-level exceptions that should be converted
 * to structured JSON responses.
 *
 * This class lives in the HTTP layer:
 * - stable error code for frontend
 * - HTTP status code
 * - UI feedback type (error, warning, info...)
 */
abstract class AbstractApiException extends \RuntimeException implements ApiExceptionInterface
{
    /**
     * @var string
     */
    private string $errorCode;

    /**
     * @var int
     */
    private int $httpStatus;

    /**
     * @var string
     */
    private string $type;

    /**
     * @param string          $message     Human-readable error message
     * @param string          $errorCode   Stable technical error code for clients
     * @param int             $httpStatus  HTTP status code
     * @param string          $type        UI feedback type (error, warning, info...)
     * @param \Throwable|null $previous    Optional previous exception
     */
    public function __construct(
        string $message,
        string $errorCode = 'ERROR',
        int $httpStatus = Response::HTTP_BAD_REQUEST,
        string $type = 'error',
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);

        $this->errorCode  = $errorCode;
        $this->httpStatus = $httpStatus;
        $this->type       = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }
}
