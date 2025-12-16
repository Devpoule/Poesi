<?php

namespace App\Http\Controller;

use App\Domain\Exception\NotFound\PoemNotFoundException;
use App\Domain\Service\PoemService;
use App\Http\Exception\ApiExceptionInterface;
use App\Http\Exception\ValidationException;
use App\Http\Mapper\PoemMapper;
use App\Http\Request\Poem\CreatePoemRequest;
use App\Http\Request\Poem\UpdatePoemRequest;
use App\Http\Response\ApiResponseFactory;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * HTTP API controller exposing CRUD operations for poems.
 */
#[Route('/api/poems', name: 'api_poems_')]
final class PoemController extends AbstractController
{
    public function __construct(
        private readonly PoemService $poemService,
        private readonly PoemMapper $poemMapper,
    ) {
    }

    /**
     * List all poems.
     *
     * GET /api/poems
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $poems = $this->poemService->listAll();

        return ApiResponseFactory::success(
            data: $this->poemMapper->toCollection($poems),
            message: 'Poems list retrieved.'
        );
    }

    /**
     * Get poem details by id.
     *
     * GET /api/poems/{id}
     */
    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $poem = $this->poemService->getPoemOrFail($id);

            return ApiResponseFactory::success(
                data: $this->poemMapper->toArray($poem),
                message: 'Poem retrieved.'
            );
        } catch (PoemNotFoundException $e) {
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
     * Create a new draft poem.
     *
     * POST /api/poems
     */
    #[Route('/add', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $dto = CreatePoemRequest::fromHttpRequest($request);

            $poem = $this->poemService->createDraft(
                authorId: $dto->getAuthorId(),
                title: $dto->getTitle(),
                content: $dto->getContent(),
                moodColor: $dto->getMoodColor()
            );

            return ApiResponseFactory::success(
                data: $this->poemMapper->toArray($poem),
                message: 'Poem created successfully.',
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
     * Update an existing poem.
     *
     * PUT /api/poems/{id}
     */
    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $dto = UpdatePoemRequest::fromHttpRequest($request);

            $poem = $this->poemService->updatePoem(
                poemId: $id,
                title: $dto->getTitle(),
                content: $dto->getContent(),
                moodColor: $dto->getMoodColor()
            );

            return ApiResponseFactory::success(
                data: $this->poemMapper->toArray($poem),
                message: 'Poem updated successfully.'
            );
        } catch (ValidationException $e) {
            return ApiResponseFactory::validationError(
                message: $e->getMessage(),
                errors: $e->getErrors(),
                code: $e->getErrorCode()
            );
        } catch (PoemNotFoundException $e) {
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
     * Publish a poem.
     *
     * POST /api/poems/{id}/publish
     */
    #[Route('/{id<\d+>}/publish', name: 'publish', methods: ['POST'])]
    public function publish(int $id): JsonResponse
    {
        try {
            $poem = $this->poemService->publish($id);

            return ApiResponseFactory::success(
                data: $this->poemMapper->toArray($poem),
                message: 'Poem published successfully.'
            );
        } catch (PoemNotFoundException $e) {
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
     * Delete a poem.
     *
     * DELETE /api/poems/{id}
     */
    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->poemService->deletePoem($id);

            return ApiResponseFactory::success(
                data: null,
                message: 'Poem deleted successfully.'
            );
        } catch (PoemNotFoundException $e) {
            return ApiResponseFactory::notFound($e->getMessage());
        } catch (ForeignKeyConstraintViolationException) {
            // Typical case: poem is still referenced by FeatherVote (if DB FK is not ON DELETE CASCADE).
            return ApiResponseFactory::error(
                message: 'Cannot delete poem because it is still referenced by other resources.',
                code: 'POEM_DELETE_CONFLICT',
                type: 'warning',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_CONFLICT
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
}
