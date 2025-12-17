<?php

namespace App\Http\Controller;

use App\Domain\Service\FeatherVoteService;
use App\Http\Request\FeatherVote\CreateFeatherVoteRequest;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\FeatherVoteResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/feather-votes', name: 'api_feather_votes_')]
final class FeatherVoteController extends AbstractController
{
    public function __construct(
        private readonly FeatherVoteService $featherVoteService,
        private readonly FeatherVoteResponse $featherVoteResponse,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return ApiResponseFactory::success(
            data: $this->featherVoteResponse->collection($this->featherVoteService->listAll()),
            message: 'Feather votes list retrieved.'
        );
    }

    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $vote = $this->featherVoteService->getVoteOrFail($id);

        return ApiResponseFactory::success(
            data: $this->featherVoteResponse->item($vote),
            message: 'Feather vote retrieved.'
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $dto = CreateFeatherVoteRequest::fromHttpRequest($request);

        $result = $this->featherVoteService->castVote(
            voterAuthorId: $dto->getVoterAuthorId(),
            poemId: $dto->getPoemId(),
            featherType: $dto->getFeatherType()
        );

        return ApiResponseFactory::success(
            data: $this->featherVoteResponse->item($result['vote']),
            message: $result['created'] ? 'Feather vote created successfully.' : 'Feather vote updated successfully.',
            meta: ['created' => (bool) $result['created']],
            httpStatus: $result['created'] ? Response::HTTP_CREATED : Response::HTTP_OK
        );
    }

    #[Route('/poem/{poemId<\d+>}', name: 'list_for_poem', methods: ['GET'])]
    public function listForPoem(int $poemId): JsonResponse
    {
        return ApiResponseFactory::success(
            data: $this->featherVoteResponse->collection($this->featherVoteService->listVotesForPoem($poemId)),
            message: 'Feather votes for poem retrieved.'
        );
    }

    #[Route('/author/{authorId<\d+>}', name: 'list_for_author', methods: ['GET'])]
    public function listForAuthor(int $authorId): JsonResponse
    {
        return ApiResponseFactory::success(
            data: $this->featherVoteResponse->collection($this->featherVoteService->listVotesByAuthor($authorId)),
            message: 'Feather votes for author retrieved.'
        );
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->featherVoteService->deleteVote($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'Feather vote deleted successfully.'
        );
    }
}
