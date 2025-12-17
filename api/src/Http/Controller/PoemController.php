<?php

namespace App\Http\Controller;

use App\Domain\Service\PoemService;
use App\Http\Request\Poem\CreatePoemRequest;
use App\Http\Request\Poem\UpdatePoemRequest;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\PoemResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/poems', name: 'api_poems_')]
final class PoemController extends AbstractController
{
    public function __construct(
        private readonly PoemService $poemService,
        private readonly PoemResponse $poemResponse,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return ApiResponseFactory::success(
            data: $this->poemResponse->collection($this->poemService->listAll()),
            message: 'Poems list retrieved.'
        );
    }

    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $poem = $this->poemService->getPoemOrFail($id);

        return ApiResponseFactory::success(
            data: $this->poemResponse->item($poem),
            message: 'Poem retrieved.'
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $dto = CreatePoemRequest::fromHttpRequest($request);

        $poem = $this->poemService->createDraft(
            authorId: $dto->getAuthorId(),
            title: $dto->getTitle(),
            content: $dto->getContent(),
            moodColor: $dto->getMoodColor()
        );

        return ApiResponseFactory::success(
            data: $this->poemResponse->item($poem),
            message: 'Poem created successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $dto = UpdatePoemRequest::fromHttpRequest($request);

        $poem = $this->poemService->updatePoem(
            poemId: $id,
            title: $dto->getTitle(),
            content: $dto->getContent(),
            moodColor: $dto->getMoodColor()
        );

        return ApiResponseFactory::success(
            data: $this->poemResponse->item($poem),
            message: 'Poem updated successfully.'
        );
    }

    #[Route('/{id<\d+>}/publish', name: 'publish', methods: ['POST'])]
    public function publish(int $id): JsonResponse
    {
        $poem = $this->poemService->publish($id);

        return ApiResponseFactory::success(
            data: $this->poemResponse->item($poem),
            message: 'Poem published successfully.'
        );
    }

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
