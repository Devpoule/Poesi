<?php

namespace App\Http\Controller;

use App\Domain\Enum\FeatherType;
use App\Domain\Service\FeatherVoteService;
use App\Http\Mapper\FeatherVoteMapper;
use App\Http\Response\ApiResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * HTTP API controller managing feather votes on poems.
 */
#[Route('/api/feather-votes', name: 'api_feather_votes_')]
class FeatherVoteController extends AbstractController
{
    public function __construct(
        private readonly FeatherVoteService $featherVoteService,
        private readonly FeatherVoteMapper $featherVoteMapper,
    ) {
    }

    /**
     * List all feather votes.
     *
     * GET /api/feather-votes
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $votes = $this->featherVoteService->listAll();

        return ApiResponseFactory::success(
            data: $this->featherVoteMapper->toCollection($votes),
            message: 'Feather votes list retrieved.'
        );
    }

    /**
     * Get a single feather vote by id.
     *
     * GET /api/feather-votes/{id}
     */
    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $vote = $this->featherVoteService->getVoteOrFail($id);

        return ApiResponseFactory::success(
            data: $this->featherVoteMapper->toArray($vote),
            message: 'Feather vote retrieved.'
        );
    }

    /**
     * Cast a new feather vote.
     *
     * Expected JSON body:
     * {
     *   "voterAuthorId": 1,
     *   "poemId": 5,
     *   "featherType": "bronze" // bronze, silver, gold
     * }
     *
     * POST /api/feather-votes
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

        $voterAuthorId = $payload['voterAuthorId'] ?? null;
        $poemId        = $payload['poemId'] ?? null;
        $featherType   = $payload['featherType'] ?? null;

        $errors = [];

        if ($voterAuthorId === null) {
            $errors['voterAuthorId'][] = 'voterAuthorId is required.';
        }

        if ($poemId === null) {
            $errors['poemId'][] = 'poemId is required.';
        }

        $featherTypeEnum = null;
        if ($featherType === null) {
            $errors['featherType'][] = 'featherType is required.';
        } else {
            try {
                $featherTypeEnum = FeatherType::from($featherType);
            } catch (\ValueError $e) {
                $errors['featherType'][] = 'Invalid featherType value.';
            }
        }

        if (!empty($errors)) {
            return ApiResponseFactory::validationError(
                message: 'Invalid feather vote payload.',
                errors: $errors
            );
        }

        $vote = $this->featherVoteService->castVote(
            voterAuthorId: (int) $voterAuthorId,
            poemId: (int) $poemId,
            featherType: $featherTypeEnum
        );

        return ApiResponseFactory::success(
            data: $this->featherVoteMapper->toArray($vote),
            message: 'Feather vote created successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * List all votes for a given poem.
     *
     * GET /api/feather-votes/poem/{poemId}
     */
    #[Route('/poem/{poemId<\d+>}', name: 'list_for_poem', methods: ['GET'])]
    public function listForPoem(int $poemId): JsonResponse
    {
        $votes = $this->featherVoteService->listVotesForPoem($poemId);

        return ApiResponseFactory::success(
            data: $this->featherVoteMapper->toCollection($votes),
            message: 'Feather votes for poem retrieved.'
        );
    }

    /**
     * List all votes cast by a given author.
     *
     * GET /api/feather-votes/author/{authorId}
     */
    #[Route('/author/{authorId<\d+>}', name: 'list_for_author', methods: ['GET'])]
    public function listForAuthor(int $authorId): JsonResponse
    {
        $votes = $this->featherVoteService->listVotesByAuthor($authorId);

        return ApiResponseFactory::success(
            data: $this->featherVoteMapper->toCollection($votes),
            message: 'Feather votes for author retrieved.'
        );
    }

    /**
     * Delete a feather vote.
     *
     * DELETE /api/feather-votes/{id}
     */
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
