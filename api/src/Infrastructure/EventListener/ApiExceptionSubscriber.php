<?php

namespace App\Infrastructure\EventListener;

use App\Domain\Exception\NotFound\AuthorNotFoundException;
use App\Domain\Exception\CannotDelete\CannotDeleteAuthorException;
use App\Domain\Exception\NotFound\PoemNotFoundException;
use App\Domain\Exception\NotFound\RewardNotFoundException;
use App\Domain\Exception\NotFound\TotemNotFoundException;
use App\Domain\Exception\NotFound\UserNotFoundException;
use App\Http\Exception\ApiExceptionInterface;
use App\Http\Exception\ValidationException;
use App\Http\Response\ApiResponseFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Converts exceptions thrown by the application into a consistent JSON API response.
 *
 * Goals:
 * - Keep controllers and services clean (throw exceptions, do not build responses).
 * - Ensure the frontend always receives the same response structure.
 * - Log errors in a centralized place.
 *
 * Exception strategy:
 * 1) Http exceptions implementing ApiExceptionInterface:
 *    - Already carry HTTP status + code + type -> direct conversion.
 * 2) ValidationException (HTTP layer):
 *    - Returns 400 with structured field errors.
 * 3) Domain exceptions (HTTP-agnostic):
 *    - Mapped here to appropriate HTTP status + error code.
 * 4) Any other Throwable:
 *    - Returns 500 with a generic message (do not leak internal details).
 */
final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @param LoggerInterface $logger Application logger (Monolog, BetterStack handler, etc.)
     */
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * We listen on KernelEvents::EXCEPTION to intercept exceptions before Symfony
     * builds the default HTML error page (we want JSON instead).
     *
     * @return array<string, array<int, mixed>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 10],
        ];
    }

    /**
     * Converts any exception into a JsonResponse using ApiResponseFactory.
     *
     * Notes:
     * - Never return raw stack traces to clients.
     * - Log with enough context to debug.
     * - Keep error codes stable (frontend can rely on them).
     *
     * @param ExceptionEvent $event
     *
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        // Always log the exception. For domain/validation errors, keep it as warning/info.
        // For unexpected exceptions, keep it as error.
        $this->logThrowable($event);

        /**
         * 1) HTTP-layer exceptions (already response-ready).
         */
        if ($throwable instanceof ApiExceptionInterface) {
            $event->setResponse(
                ApiResponseFactory::error(
                    message: $throwable->getMessage(),
                    code: $throwable->getErrorCode(),
                    type: $throwable->getType(),
                    errors: null,
                    data: null,
                    httpStatus: $throwable->getHttpStatus()
                )
            );

            return;
        }

        /**
         * 2) Validation errors (structured field errors).
         */
        if ($throwable instanceof ValidationException) {
            $event->setResponse(
                ApiResponseFactory::validationError(
                    message: $throwable->getMessage(),
                    errors: $throwable->getErrors(),
                    code: $throwable->getErrorCode()
                )
            );

            return;
        }

        /**
         * 3) Domain exceptions mapping (HTTP-agnostic -> HTTP response here).
         * Add cases progressively as your API grows.
         */

        // Author
        if ($throwable instanceof AuthorNotFoundException) {
            $event->setResponse(
                ApiResponseFactory::notFound(
                    message: $throwable->getMessage(),
                    code: 'AUTHOR_NOT_FOUND'
                )
            );

            return;
        }

        if ($throwable instanceof CannotDeleteAuthorException) {
            $event->setResponse(
                ApiResponseFactory::error(
                    message: $throwable->getMessage(),
                    code: 'AUTHOR_DELETE_CONFLICT',
                    type: 'conflict',
                    errors: null,
                    data: null,
                    httpStatus: Response::HTTP_CONFLICT
                )
            );

            return;
        }

        // Totem
        if ($throwable instanceof TotemNotFoundException) {
            $event->setResponse(
                ApiResponseFactory::notFound(
                    message: $throwable->getMessage(),
                    code: 'TOTEM_NOT_FOUND'
                )
            );

            return;
        }

        // Poem
        if ($throwable instanceof PoemNotFoundException) {
            $event->setResponse(
                ApiResponseFactory::notFound(
                    message: $throwable->getMessage(),
                    code: 'POEM_NOT_FOUND'
                )
            );

            return;
        }

        // Reward
        if ($throwable instanceof RewardNotFoundException) {
            $event->setResponse(
                ApiResponseFactory::notFound(
                    message: $throwable->getMessage(),
                    code: 'REWARD_NOT_FOUND'
                )
            );

            return;
        }

        // User
        if ($throwable instanceof UserNotFoundException) {
            $event->setResponse(
                ApiResponseFactory::notFound(
                    message: $throwable->getMessage(),
                    code: 'USER_NOT_FOUND'
                )
            );

            return;
        }

        /**
         * 4) Fallback: internal server error.
         *
         * We do not expose technical details. The logs already contain them.
         */
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
     * Logs the exception with request context.
     *
     * @param ExceptionEvent $event
     *
     * @return void
     */
    private function logThrowable(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        $request   = $event->getRequest();

        $context = [
            'exception_class' => $throwable::class,
            'message'         => $throwable->getMessage(),
            'path'            => $request->getPathInfo(),
            'method'          => $request->getMethod(),
            'query'           => $request->query->all(),
        ];

        // Domain/validation errors are expected: keep lower severity.
        if ($throwable instanceof ValidationException) {
            $context['validation_errors'] = $throwable->getErrors();
            $this->logger->warning('API validation error.', $context);
            return;
        }

        if (
            $throwable instanceof AuthorNotFoundException ||
            $throwable instanceof TotemNotFoundException ||
            $throwable instanceof PoemNotFoundException ||
            $throwable instanceof RewardNotFoundException ||
            $throwable instanceof UserNotFoundException ||
            $throwable instanceof CannotDeleteAuthorException
        ) {
            $this->logger->info('API domain exception.', $context);
            return;
        }

        // Unexpected exception: log full stack trace as error.
        $context['trace'] = $throwable->getTraceAsString();
        $this->logger->error('API unexpected exception.', $context);
    }
}
