<?php

namespace App\Http\Controller;

use App\Domain\Service\MoodService;
use App\Http\Request\Mood\CreateMoodRequest;
use App\Http\Request\Mood\UpdateMoodRequest;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\MoodResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/moods', name: 'api_moods_')]
final class MoodController extends AbstractController
{
    public function __construct(
        private readonly MoodService $moodService,
        private readonly MoodResponse $moodResponse,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return ApiResponseFactory::success(
            data: $this->moodResponse->collection($this->moodService->listAll()),
            message: 'Moods list retrieved.'
        );
    }

    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $mood = $this->moodService->getOrFail($id);

        return ApiResponseFactory::success(
            data: $this->moodResponse->item($mood),
            message: 'Mood retrieved.'
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $dto = CreateMoodRequest::fromHttpRequest($request);

        $mood = $this->moodService->create(
            key: $dto->getKey(),
            label: $dto->getLabel(),
            description: $dto->getDescription(),
            icon: $dto->getIcon()
        );

        return ApiResponseFactory::success(
            data: $this->moodResponse->item($mood),
            message: 'Mood created successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $dto = UpdateMoodRequest::fromHttpRequest($request);

        $mood = $this->moodService->update(
            id: $id,
            key: $dto->getKey(),
            label: $dto->getLabel(),
            description: $dto->getDescription(),
            icon: $dto->getIcon()
        );

        return ApiResponseFactory::success(
            data: $this->moodResponse->item($mood),
            message: 'Mood updated successfully.'
        );
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->moodService->delete($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'Mood deleted successfully.'
        );
    }
}
