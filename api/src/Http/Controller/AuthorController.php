<?php

namespace App\Http\Controller;

use App\Domain\Service\AuthorService;
use App\Http\Exception\ApiExceptionInterface;
use App\Http\Exception\ValidationException;
use App\Http\Mapper\AuthorMapper;
use App\Http\Request\Author\CreateAuthorRequest;
use App\Http\Request\Author\UpdateAuthorRequest;
use App\Http\Response\ApiResponseFactory;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * HTTP API controller exposing CRUD operations for authors.
 */
#[Route('/api/authors', name: 'api_authors_')]
final class AuthorController extends AbstractController
{
    public function __construct(
        private readonly AuthorService $authorService,
        private readonly AuthorMapper $authorMapper,
    ) {
    }

    /**
     * List all authors.
     *
     * GET /api/authors
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $authors = $this->authorService->listAll();

            return ApiResponseFactory::success(
                data: $this->authorMapper->toCollection($authors),
                message: 'Authors list retrieved.'
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
     * Get author details by id.
     *
     * GET /api/authors/{id}
     */
    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $author = $this->authorService->getAuthorOrFail($id);

            return ApiResponseFactory::success(
                data: $this->authorMapper->toArray($author),
                message: 'Author retrieved.'
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
     * Create a new author.
     *
     * POST /api/authors/add
     */
    #[Route('/add', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $dto = CreateAuthorRequest::fromHttpRequest($request);

            $author = $this->authorService->createAuthor(
                pseudo: $dto->getPseudo(),
                email: $dto->getEmail(),
                totemId: $dto->getTotemId(),
                moodColor: $dto->getMoodColor()
            );

            return ApiResponseFactory::success(
                data: $this->authorMapper->toArray($author),
                message: 'Author created successfully.',
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
     * Update an existing author.
     *
     * PUT /api/authors/{id}
     *
     * All fields are optional; only provided keys will be updated.
     */
    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $dto = UpdateAuthorRequest::fromHttpRequest($request);

            $author = $this->authorService->updateAuthor(
                authorId: $id,
                pseudo: $dto->getPseudo(),
                moodColor: $dto->getMoodColor(),
                totemId: $dto->getTotemId()
            );

            return ApiResponseFactory::success(
                data: $this->authorMapper->toArray($author),
                message: 'Author updated successfully.'
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
     * Delete an author.
     *
     * DELETE /api/authors/{id}
     */
    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->authorService->deleteAuthor($id);

            return ApiResponseFactory::success(
                data: null,
                message: 'Author deleted successfully.'
            );
        } catch (ForeignKeyConstraintViolationException) {
            return ApiResponseFactory::error(
                message: 'Cannot delete author because it is still referenced by other resources.',
                code: 'AUTHOR_DELETE_CONFLICT',
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
