<?php

namespace App\Http\Controller;

use App\Domain\Exception\NotFound\AuthorNotFoundException;
use App\Domain\Exception\NotFound\PoemNotFoundException;
use App\Domain\Service\FeatherVoteService;
use App\Http\Exception\ApiExceptionInterface;
use App\Http\Exception\ValidationException;
use App\Http\Mapper\FeatherVoteMapper;
use App\Http\Request\FeatherVote\CreateFeatherVoteRequest;
use App\Http\Response\ApiResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * HTTP API controller exposing operations for feather votes.
 */
#[Route('/api/feather-votes', name: 'api_feather_votes_')]
final class FeatherVoteController extends AbstractController
{
    public function __construct(
        private readonly FeatherVoteService $featherVoteService,
        private readonly FeatherVoteMapper $featherVoteMapper,
    ) {
    }

    /**
     * List all votes.
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
     * Show a vote by id.
     *
     * GET /api/feather-votes/{id}
     */
    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $vote = $this->featherVoteService->getVoteOrFail($id);

            return ApiResponseFactory::success(
                data: $this->featherVoteMapper->toArray($vote),
                message: 'Feather vote retrieved.'
            );
        } catch (\RuntimeException $e) {
            return ApiResponseFactory::notFound($e->getMessage());
        }
    }

    /**
     * Create (or update) a vote.
     *
     * POST /api/feather-votes
     *
     * If the vote already exists for (voter, poem), the service updates it.
     */
    #[Route('/add', name: 'create', methods: ['POST'])]
    public function create(\Symfony\Component\HttpFoundation\Request $request): JsonResponse
    {
        try {
            $dto = CreateFeatherVoteRequest::fromHttpRequest($request);

            $result = $this->featherVoteService->castVote(
                voterAuthorId: $dto->getVoterAuthorId(),
                poemId: $dto->getPoemId(),
                featherType: $dto->getFeatherType()
            );

            $vote = $result['vote'];
            $created = (bool) $result['created'];

            return ApiResponseFactory::success(
                data: $this->featherVoteMapper->toArray($vote),
                message: $created ? 'Feather vote created successfully.' : 'Feather vote updated successfully.',
                meta: ['created' => $created],
                httpStatus: $created ? Response::HTTP_CREATED : Response::HTTP_OK
            );
        } catch (ValidationException $e) {
            return ApiResponseFactory::validationError(
                message: $e->getMessage(),
                errors: $e->getErrors(),
                code: $e->getErrorCode()
            );
        } catch (AuthorNotFoundException $e) {
            return ApiResponseFactory::notFound($e->getMessage());
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
     * List votes for a given poem.
     *
     * GET /api/feather-votes/poem/{poemId}
     */
    #[Route('/poem/{poemId<\d+>}', name: 'list_for_poem', methods: ['GET'])]
    public function listForPoem(int $poemId): JsonResponse
    {
        try {
            $votes = $this->featherVoteService->listVotesForPoem($poemId);

            return ApiResponseFactory::success(
                data: $this->featherVoteMapper->toCollection($votes),
                message: 'Feather votes for poem retrieved.'
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
     * List votes cast by an author.
     *
     * GET /api/feather-votes/author/{authorId}
     */
    #[Route('/author/{authorId<\d+>}', name: 'list_for_author', methods: ['GET'])]
    public function listForAuthor(int $authorId): JsonResponse
    {
        try {
            $votes = $this->featherVoteService->listVotesByAuthor($authorId);

            return ApiResponseFactory::success(
                data: $this->featherVoteMapper->toCollection($votes),
                message: 'Feather votes for author retrieved.'
            );
        } catch (AuthorNotFoundException $e) {
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
     * Delete a vote.
     *
     * DELETE /api/feather-votes/{id}
     */
    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->featherVoteService->deleteVote($id);

            return ApiResponseFactory::success(
                data: null,
                message: 'Feather vote deleted successfully.'
            );
        } catch (\RuntimeException $e) {
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
}
