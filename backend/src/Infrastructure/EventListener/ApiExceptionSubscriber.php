<?php

namespace App\Infrastructure\EventListener;

use App\Domain\Exception\CannotDelete\CannotDeleteException;
use App\Domain\Exception\CannotPublish\CannotPublishPoemException as CannotPublishCannotPublishPoemException;
use App\Domain\Exception\Conflict\ConflictException;
use App\Domain\Exception\DomainException;
use App\Domain\Exception\NotFound\NotFoundException;
use App\Http\Exception\ApiExceptionInterface;
use App\Http\Exception\ValidationException;
use App\Http\Response\ApiResponseFactory;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Converts exceptions thrown by the application into a consistent JSON API response.
 *
 * Strategy:
 * - ValidationException: structured 400
 * - ApiExceptionInterface: response-ready (HTTP layer)
 * - DomainException: mapped by "family" to HTTP status
 * - DB FK violation: 409 generic conflict
 * - Fallback: 500 generic
 */
final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 10],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        $this->logThrowable($event);

        // 1) Validation errors (FIRST).
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

        // 2) HTTP-layer exceptions (already carry httpStatus/code/type).
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

        // 2.5) Security access errors.
        if ($throwable instanceof AccessDeniedException) {
            $event->setResponse(
                ApiResponseFactory::error(
                    message: 'Access denied.',
                    code: 'FORBIDDEN',
                    type: 'error',
                    errors: null,
                    data: null,
                    httpStatus: Response::HTTP_FORBIDDEN
                )
            );
            return;
        }

        // 3) Domain exceptions mapping (HTTP-agnostic -> HTTP here).
        if ($throwable instanceof DomainException) {
            $httpStatus = match (true) {
                $throwable instanceof NotFoundException => Response::HTTP_NOT_FOUND,
                $throwable instanceof CannotDeleteException => Response::HTTP_CONFLICT,
                $throwable instanceof ConflictException => Response::HTTP_CONFLICT,
                $throwable instanceof CannotPublishCannotPublishPoemException => Response::HTTP_CONFLICT,
                default => Response::HTTP_BAD_REQUEST,
            };

            $type = match ($httpStatus) {
                Response::HTTP_CONFLICT => 'warning',
                default => 'error',
            };

            $event->setResponse(
                ApiResponseFactory::error(
                    message: $throwable->getMessage(),
                    code: $throwable->getErrorCode(),
                    type: $type,
                    errors: null,
                    data: null,
                    httpStatus: $httpStatus
                )
            );
            return;
        }

        // 4) Specific DB exceptions mapping (generic conflict).
        if ($throwable instanceof ForeignKeyConstraintViolationException) {
            $event->setResponse(
                ApiResponseFactory::error(
                    message: 'Resource cannot be deleted because it is still referenced by other resources.',
                    code: 'DELETE_CONFLICT',
                    type: 'warning',
                    errors: null,
                    data: null,
                    httpStatus: Response::HTTP_CONFLICT
                )
            );
            return;
        }

        // 5) HTTP exceptions (routing, method not allowed, etc.).
        if ($throwable instanceof HttpExceptionInterface) {
            $statusCode = $throwable->getStatusCode();
            $code = match ($statusCode) {
                Response::HTTP_NOT_FOUND => 'RESOURCE_NOT_FOUND',
                Response::HTTP_METHOD_NOT_ALLOWED => 'METHOD_NOT_ALLOWED',
                default => 'HTTP_ERROR',
            };

            $event->setResponse(
                ApiResponseFactory::error(
                    message: $throwable->getMessage(),
                    code: $code,
                    type: 'error',
                    errors: null,
                    data: null,
                    httpStatus: $statusCode
                )
            );
            return;
        }

        // 5) Fallback.
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

    private function logThrowable(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        $request = $event->getRequest();

        $context = [
            'exception_class' => $throwable::class,
            'message' => $throwable->getMessage(),
            'path' => $request->getPathInfo(),
            'method' => $request->getMethod(),
            'query' => $request->query->all(),
        ];

        if ($throwable instanceof ValidationException) {
            $context['validation_errors'] = $throwable->getErrors();
            $this->logger->warning('API validation error.', $context);
            return;
        }

        if ($throwable instanceof DomainException) {
            $context['domain_code'] = $throwable->getErrorCode();
            $this->logger->info('API domain exception.', $context);
            return;
        }

        if ($throwable instanceof ApiExceptionInterface) {
            $context['api_code'] = $throwable->getErrorCode();
            $context['http_status'] = $throwable->getHttpStatus();
            $this->logger->warning('API http exception.', $context);
            return;
        }

        $context['trace'] = $throwable->getTraceAsString();
        $this->logger->error('API unexpected exception.', $context);
    }
}
