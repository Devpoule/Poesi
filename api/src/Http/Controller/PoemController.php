<?php

namespace App\Http\Controller;

use App\Domain\Service\PoemService;
use App\Http\Request\CreatePoemRequest;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\PoemResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Poem API endpoints.
 */
#[Route('/api/poems', name: 'api_poems_')]
final class PoemController extends AbstractController
{
    public function __construct(
        private PoemService $poemService
    ) {
    }

    /**
     * Create a poem draft.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        // Any ValidationException thrown here will be handled globally by ApiExceptionSubscriber.
        $createRequest = CreatePoemRequest::fromHttpRequest($request);

        // Any domain exception (AuthorNotFoundException, etc.) will also be mapped globally.
        $poem = $this->poemService->createDraft(
            authorId: $createRequest->getAuthorId(),
            title: $createRequest->getTitle(),
            content: $createRequest->getContent(),
            moodColor: $createRequest->getMoodColor()
        );

        $dto = PoemResponseFactory::fromEntity($poem);

        return ApiResponseFactory::success(
            data: $dto->toArray(),
            message: 'Poem created.',
            code: 'POEM_CREATED'
        );
    }

    /**
     * Delete a poem.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        // If poem has votes, CannotDeletePoemWithVotesException will be handled globally.
        $this->poemService->deletePoem($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'Poem deleted.',
            code: 'POEM_DELETED'
        );
    }
}
