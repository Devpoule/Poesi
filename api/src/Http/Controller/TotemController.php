<?php

namespace App\Http\Controller;

use App\Domain\Exception\NotFound\TotemNotFoundException;
use App\Domain\Service\TotemService;
use App\Http\Exception\ApiExceptionInterface;
use App\Http\Exception\ValidationException;
use App\Http\Mapper\TotemMapper;
use App\Http\Request\Totem\CreateTotemRequest;
use App\Http\Request\Totem\UpdateTotemRequest;
use App\Http\Response\ApiResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * HTTP API controller exposing CRUD operations for totems.
 */
#[Route('/api/totems', name: 'api_totems_')]
final class TotemController extends AbstractController
{
    public function __construct(
        private readonly TotemService $totemService,
        private readonly TotemMapper $totemMapper,
    ) {
    }

    /**
     * List all totems.
     *
     * GET /api/totems
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $totems = $this->totemService->listAll();

        return ApiResponseFactory::success(
            data: $this->totemMapper->toCollection($totems),
            message: 'Totems list retrieved.'
        );
    }

    /**
     * Get totem details by id.
     *
     * GET /api/totems/{id}
     */
    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $totem = $this->totemService->getTotemOrFail($id);

            return ApiResponseFactory::success(
                data: $this->totemMapper->toArray($totem),
                message: 'Totem retrieved.'
            );
        } catch (TotemNotFoundException $e) {
            return ApiResponseFactory::notFound($e->getMessage());
        } catch (\Throwable) {
            return ApiResponseFactory::error(
                message: 'Unexpected server error.',
                code: 'UNEXPECTED_ERROR',
                type: 'error',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Create a totem.
     *
     * POST /api/totems
     */
    #[Route('/add', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $dto = CreateTotemRequest::fromHttpRequest($request);

            $totem = $this->totemService->createTotem(
                name: $dto->getName(),
                description: $dto->getDescription(),
                picture: $dto->getPicture()
            );

            return ApiResponseFactory::success(
                data: $this->totemMapper->toArray($totem),
                message: 'Totem created successfully.',
                httpStatus: Response::HTTP_CREATED
            );
        } catch (ValidationException $e) {
            return ApiResponseFactory::validationError(
                message: $e->getMessage(),
                errors: $e->getErrors(),
                code: $e->getErrorCode()
            );
        } catch (ApiExceptionInterface $e) {
            return ApiResponseFactory::error(
                message: $e->getMessage(),
                code: $e->getErrorCode(),
                type: $e->getType(),
                errors: null,
                data: null,
                httpStatus: $e->getHttpStatus()
            );
        } catch (\Throwable) {
            return ApiResponseFactory::error(
                message: 'Unexpected server error.',
                code: 'UNEXPECTED_ERROR',
                type: 'error',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update a totem.
     *
     * PUT /api/totems/{id}
     */
    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $dto = UpdateTotemRequest::fromHttpRequest($request);

            $totem = $this->totemService->updateTotem(
                totemId: $id,
                name: $dto->getName(),
                description: $dto->getDescription(),
                picture: $dto->getPicture()
            );

            return ApiResponseFactory::success(
                data: $this->totemMapper->toArray($totem),
                message: 'Totem updated successfully.'
            );
        } catch (ValidationException $e) {
            return ApiResponseFactory::validationError(
                message: $e->getMessage(),
                errors: $e->getErrors(),
                code: $e->getErrorCode()
            );
        } catch (TotemNotFoundException $e) {
            return ApiResponseFactory::notFound($e->getMessage());
        } catch (ApiExceptionInterface $e) {
            return ApiResponseFactory::error(
                message: $e->getMessage(),
                code: $e->getErrorCode(),
                type: $e->getType(),
                errors: null,
                data: null,
                httpStatus: $e->getHttpStatus()
            );
        } catch (\Throwable) {
            return ApiResponseFactory::error(
                message: 'Unexpected server error.',
                code: 'UNEXPECTED_ERROR',
                type: 'error',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Delete a totem.
     *
     * DELETE /api/totems/{id}
     */
    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->totemService->deleteTotem($id);

            return ApiResponseFactory::success(
                data: null,
                message: 'Totem deleted successfully.'
            );
        } catch (TotemNotFoundException $e) {
            return ApiResponseFactory::notFound($e->getMessage());
        } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException) {
            return ApiResponseFactory::error(
                message: 'Cannot delete totem because it is still referenced by other resources.',
                code: 'TOTEM_DELETE_CONFLICT',
                type: 'warning',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_CONFLICT
            );
        } catch (\Throwable) {
            return ApiResponseFactory::error(
                message: 'Unexpected server error.',
                code: 'UNEXPECTED_ERROR',
                type: 'error',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
