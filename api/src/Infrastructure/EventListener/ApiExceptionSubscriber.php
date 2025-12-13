<?php

namespace App\Infrastructure\EventListener;

use App\Domain\Exception\AuthorNotFoundException;
use App\Domain\Exception\CannotDeletePoemWithVotesException;
use App\Domain\Exception\PoemNotFoundException;
use App\Http\Exception\ApiExceptionInterface;
use App\Http\Exception\ValidationException;
use App\Http\Response\ApiResponseFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Converts exceptions into standardized JSON API error responses.
 *
 * This avoids repeating try/catch blocks across controllers and
 * guarantees a consistent contract to mobile/web clients.
 */
final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        // 1) HTTP-aware exceptions (already shaped for API responses)
        if ($throwable instanceof ApiExceptionInterface) {
            $event->setResponse($this->buildFromApiException($throwable));
            return;
        }

        // 2) Domain exceptions mapping (HTTP-agnostic domain -> HTTP response here)
        if ($throwable instanceof PoemNotFoundException) {
            $event->setResponse(
                ApiResponseFactory::error(
                    message: $throwable->getMessage(),
                    code: 'POEM_NOT_FOUND',
                    type: 'error',
                    errors: null,
                    data: null,
                    httpStatus: Response::HTTP_NOT_FOUND
                )
            );
            return;
        }

        if ($throwable instanceof AuthorNotFoundException) {
            $event->setResponse(
                ApiResponseFactory::error(
                    message: $throwable->getMessage(),
                    code: 'AUTHOR_NOT_FOUND',
                    type: 'error',
                    errors: null,
                    data: null,
                    httpStatus: Response::HTTP_NOT_FOUND
                )
            );
            return;
        }

        if ($throwable instanceof CannotDeletePoemWithVotesException) {
            $event->setResponse(
                ApiResponseFactory::error(
                    message: $throwable->getMessage(),
                    code: 'POEM_HAS_VOTES',
                    type: 'conflict',
                    errors: null,
                    data: null,
                    httpStatus: Response::HTTP_CONFLICT
                )
            );
            return;
        }

        // 3) Unexpected errors -> 500 + logs
        $this->logger->error('Unexpected exception.', [
            'exception_class' => $throwable::class,
            'message' => $throwable->getMessage(),
        ]);

        $event->setResponse(
            ApiResponseFactory::error(
                message: 'An unexpected error occurred.',
                code: 'INTERNAL_ERROR',
                type: 'error',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_INTERNAL_SERVER_ERROR
            )
        );
    }

    /**
     * Convert ApiExceptionInterface instances to JSON responses.
     *
     * @param ApiExceptionInterface $exception
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function buildFromApiException(ApiExceptionInterface $exception): \Symfony\Component\HttpFoundation\JsonResponse
    {
        if ($exception instanceof ValidationException) {
            return ApiResponseFactory::validationError(
                message: $exception->getMessage(),
                errors: $exception->getErrors(),
                code: $exception->getErrorCode()
            );
        }

        return ApiResponseFactory::error(
            message: $exception->getMessage(),
            code: $exception->getErrorCode(),
            type: $exception->getType(),
            errors: null,
            data: null,
            httpStatus: $exception->getHttpStatus()
        );
    }
}
