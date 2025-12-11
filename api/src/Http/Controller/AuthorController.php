<?php

namespace App\Http\Controller;

use App\Domain\Enum\MoodColor;
use App\Domain\Service\AuthorService;
use App\Http\Mapper\AuthorMapper;
use App\Http\Response\ApiResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * HTTP API controller exposing CRUD operations for authors.
 */
#[Route('/api/authors', name: 'api_authors_')]
class AuthorController extends AbstractController
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
        $authors = $this->authorService->listAll();

        return ApiResponseFactory::success(
            data: $this->authorMapper->toCollection($authors),
            message: 'Authors list retrieved.'
        );
    }

    /**
     * Get author details by id.
     *
     * GET /api/authors/{id}
     */
    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $author = $this->authorService->getAuthorOrFail($id);

        return ApiResponseFactory::success(
            data: $this->authorMapper->toArray($author),
            message: 'Author retrieved.'
        );
    }

    /**
     * Create a new author.
     *
     * Expected JSON body:
     * {
     *   "pseudo": "Tito",
     *   "email": "tito@example.com",
     *   "totemId": 1,           // optional
     *   "moodColor": "blue"     // optional, one of MoodColor values
     * }
     *
     * POST /api/authors
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return ApiResponseFactory::validationError(
                message: 'Invalid JSON payload.',
                errors: ['body' => ['Request body must be valid JSON.']]
            );
        }

        $pseudo = $payload['pseudo'] ?? null;
        $email  = $payload['email'] ?? null;

        $errors = [];

        if ($pseudo === null || trim((string) $pseudo) === '') {
            $errors['pseudo'][] = 'Pseudo is required.';
        }

        if ($email === null || trim((string) $email) === '') {
            $errors['email'][] = 'Email is required.';
        }

        if (!empty($errors)) {
            return ApiResponseFactory::validationError(
                message: 'Invalid author payload.',
                errors: $errors
            );
        }

        $totemId   = isset($payload['totemId']) ? (int) $payload['totemId'] : null;
        $moodColor = null;

        if (isset($payload['moodColor'])) {
            try {
                $moodColor = MoodColor::from($payload['moodColor']);
            } catch (\ValueError $e) {
                $errors['moodColor'][] = 'Invalid moodColor value.';
            }
        }

        if (!empty($errors)) {
            return ApiResponseFactory::validationError(
                message: 'Invalid author payload.',
                errors: $errors
            );
        }

        $author = $this->authorService->createAuthor(
            pseudo: (string) $pseudo,
            email: (string) $email,
            totemId: $totemId,
            moodColor: $moodColor
        );

        return ApiResponseFactory::success(
            data: $this->authorMapper->toArray($author),
            message: 'Author created successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * Update an existing author.
     *
     * All fields are optional; only provided keys will be updated.
     *
     * Example JSON body:
     * {
     *   "pseudo": "New pseudo",
     *   "moodColor": "red",
     *   "totemId": 2
     * }
     *
     * PUT /api/authors/{id}
     */
    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return ApiResponseFactory::validationError(
                message: 'Invalid JSON payload.',
                errors: ['body' => ['Request body must be valid JSON.']]
            );
        }

        $pseudo    = array_key_exists('pseudo', $payload) ? $payload['pseudo'] : null;
        $moodColor = null;
        $totemId   = array_key_exists('totemId', $payload) ? (int) $payload['totemId'] : null;

        $errors = [];

        if (array_key_exists('moodColor', $payload)) {
            try {
                $moodColor = MoodColor::from($payload['moodColor']);
            } catch (\ValueError $e) {
                $errors['moodColor'][] = 'Invalid moodColor value.';
            }
        }

        if (!empty($errors)) {
            return ApiResponseFactory::validationError(
                message: 'Invalid author payload.',
                errors: $errors
            );
        }

        $author = $this->authorService->updateAuthor(
            authorId: $id,
            pseudo: $pseudo !== null ? (string) $pseudo : null,
            moodColor: $moodColor,
            totemId: $totemId !== 0 ? $totemId : null
        );

        return ApiResponseFactory::success(
            data: $this->authorMapper->toArray($author),
            message: 'Author updated successfully.'
        );
    }

    /**
     * Delete an author.
     *
     * DELETE /api/authors/{id}
     */
    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->authorService->deleteAuthor($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'Author deleted successfully.'
        );
    }
}
