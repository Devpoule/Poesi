<?php

namespace App\Http\Controller;

use App\Domain\Enum\MoodColor;
use App\Domain\Service\PoemService;
use App\Http\Mapper\PoemMapper;
use App\Http\Response\ApiResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * HTTP API controller exposing CRUD operations for poems.
 */
#[Route('/api/poems', name: 'api_poems_')]
class PoemController extends AbstractController
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
        $poem = $this->poemService->getPoemOrFail($id);

        return ApiResponseFactory::success(
            data: $this->poemMapper->toArray($poem),
            message: 'Poem retrieved.'
        );
    }

    /**
     * Create a new draft poem for an author.
     *
     * Expected JSON body:
     * {
     *   "authorId": 1,
     *   "title": "My poem",
     *   "content": "Text...",
     *   "moodColor": "blue"
     * }
     *
     * POST /api/poems
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

        $authorId  = $payload['authorId'] ?? null;
        $title     = $payload['title'] ?? null;
        $content   = $payload['content'] ?? null;
        $moodColor = $payload['moodColor'] ?? null;

        $errors = [];

        if ($authorId === null) {
            $errors['authorId'][] = 'authorId is required.';
        }

        if ($title === null || trim((string) $title) === '') {
            $errors['title'][] = 'title is required.';
        }

        if ($content === null || trim((string) $content) === '') {
            $errors['content'][] = 'content is required.';
        }

        $moodColorEnum = null;
        if ($moodColor !== null) {
            try {
                $moodColorEnum = MoodColor::from($moodColor);
            } catch (\ValueError $e) {
                $errors['moodColor'][] = 'Invalid moodColor value.';
            }
        } else {
            $errors['moodColor'][] = 'moodColor is required.';
        }

        if (!empty($errors)) {
            return ApiResponseFactory::validationError(
                message: 'Invalid poem payload.',
                errors: $errors
            );
        }

        $poem = $this->poemService->createDraft(
            authorId: (int) $authorId,
            title: (string) $title,
            content: (string) $content,
            moodColor: $moodColorEnum
        );

        return ApiResponseFactory::success(
            data: $this->poemMapper->toArray($poem),
            message: 'Poem created successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * Update a poem.
     *
     * All fields are optional.
     *
     * Example body:
     * {
     *   "title": "New title",
     *   "content": "New content",
     *   "moodColor": "red"
     * }
     *
     * PUT /api/poems/{id}
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

        $title     = $payload['title'] ?? null;
        $content   = $payload['content'] ?? null;
        $moodColor = $payload['moodColor'] ?? null;

        $errors = [];
        $moodColorEnum = null;

        if ($moodColor !== null) {
            try {
                $moodColorEnum = MoodColor::from($moodColor);
            } catch (\ValueError $e) {
                $errors['moodColor'][] = 'Invalid moodColor value.';
            }
        }

        if (!empty($errors)) {
            return ApiResponseFactory::validationError(
                message: 'Invalid poem payload.',
                errors: $errors
            );
        }

        $poem = $this->poemService->updatePoem(
            poemId: $id,
            title: $title !== null ? (string) $title : null,
            content: $content !== null ? (string) $content : null,
            moodColor: $moodColorEnum
        );

        return ApiResponseFactory::success(
            data: $this->poemMapper->toArray($poem),
            message: 'Poem updated successfully.'
        );
    }

    /**
     * Publish a poem.
     *
     * POST /api/poems/{id}/publish
     */
    #[Route('/{id<\d+>}/publish', name: 'publish', methods: ['POST'])]
    public function publish(int $id): JsonResponse
    {
        $poem = $this->poemService->publish($id);

        return ApiResponseFactory::success(
            data: $this->poemMapper->toArray($poem),
            message: 'Poem published successfully.'
        );
    }

    /**
     * Delete a poem.
     *
     * DELETE /api/poems/{id}
     */
    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->poemService->deletePoem($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'Poem deleted successfully.'
        );
    }
}
