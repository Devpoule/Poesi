<?php

namespace App\Http\Controller;

use App\Domain\Service\TotemService;
use App\Http\Mapper\TotemMapper;
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
class TotemController extends AbstractController
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
     * Get a single totem by id.
     *
     * GET /api/totems/{id}
     */
    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $totem = $this->totemService->getTotemOrFail($id);

        return ApiResponseFactory::success(
            data: $this->totemMapper->toArray($totem),
            message: 'Totem retrieved.'
        );
    }

    /**
     * Create a totem.
     *
     * Expected JSON body:
     * {
     *   "name": "Phoenix",
     *   "description": "Fire bird",
     *   "picture": "/images/phoenix.png"
     * }
     *
     * POST /api/totems
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

        $name        = $payload['name'] ?? null;
        $description = $payload['description'] ?? null;
        $picture     = $payload['picture'] ?? null;

        $errors = [];

        if ($name === null || trim((string) $name) === '') {
            $errors['name'][] = 'name is required.';
        }

        if (!empty($errors)) {
            return ApiResponseFactory::validationError(
                message: 'Invalid totem payload.',
                errors: $errors
            );
        }

        $totem = $this->totemService->createTotem(
            name: (string) $name,
            description: $description !== null ? (string) $description : null,
            picture: $picture !== null ? (string) $picture : null
        );

        return ApiResponseFactory::success(
            data: $this->totemMapper->toArray($totem),
            message: 'Totem created successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * Update a totem.
     *
     * PUT /api/totems/{id}
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

        $name        = $payload['name'] ?? null;
        $description = $payload['description'] ?? null;
        $picture     = $payload['picture'] ?? null;

        $totem = $this->totemService->updateTotem(
            totemId: $id,
            name: $name !== null ? (string) $name : null,
            description: $description !== null ? (string) $description : null,
            picture: $picture !== null ? (string) $picture : null
        );

        return ApiResponseFactory::success(
            data: $this->totemMapper->toArray($totem),
            message: 'Totem updated successfully.'
        );
    }

    /**
     * Delete a totem.
     *
     * DELETE /api/totems/{id}
     */
    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->totemService->deleteTotem($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'Totem deleted successfully.'
        );
    }
}
