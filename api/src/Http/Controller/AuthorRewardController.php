<?php

namespace App\Http\Controller;

use App\Domain\Exception\NotFound\AuthorNotFoundException;
use App\Domain\Exception\NotFound\RewardNotFoundException;
use App\Domain\Service\AuthorRewardService;
use App\Http\Exception\ApiExceptionInterface;
use App\Http\Exception\ValidationException;
use App\Http\Mapper\AuthorRewardMapper;
use App\Http\Request\AuthorReward\AssignAuthorRewardRequest;
use App\Http\Response\ApiResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * HTTP API controller exposing operations for rewards assigned to authors.
 *
 * Routes are aligned with the current router output:
 * - GET    /api/authors/{authorId}/rewards
 * - POST   /api/authors/{authorId}/rewards
 * - DELETE /api/author-rewards/{id}
 */
final class AuthorRewardController extends AbstractController
{
    public function __construct(
        private readonly AuthorRewardService $authorRewardService,
        private readonly AuthorRewardMapper $authorRewardMapper,
    ) {
    }

    /**
     * List rewards assigned to an author.
     *
     * GET /api/authors/{authorId}/rewards
     */
    #[Route('/api/authors/{authorId<\d+>}/rewards', name: 'api_author_rewards_list', methods: ['GET'])]
    public function listForAuthor(int $authorId): JsonResponse
    {
        try {
            $authorRewards = $this->authorRewardService->listForAuthor($authorId);

            return ApiResponseFactory::success(
                data: $this->authorRewardMapper->toCollection($authorRewards),
                message: 'Author rewards list retrieved.'
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
     * Assign a reward to an author by reward code.
     *
     * POST /api/authors/{authorId}/rewards
     */
    #[Route('/api/authors/{authorId<\d+>}/rewards', name: 'api_author_rewards_assign', methods: ['POST'])]
    public function assign(int $authorId, Request $request): JsonResponse
    {
        try {
            $dto = AssignAuthorRewardRequest::fromHttpRequest($request);

            $authorReward = $this->authorRewardService->assign(
                authorId: $authorId,
                rewardCode: $dto->getRewardCode()
            );

            return ApiResponseFactory::success(
                data: $this->authorRewardMapper->toArray($authorReward),
                message: 'Reward assigned successfully.',
                httpStatus: Response::HTTP_CREATED
            );
        } catch (ValidationException $e) {
            return ApiResponseFactory::validationError(
                message: $e->getMessage(),
                errors: $e->getErrors(),
                code: $e->getErrorCode()
            );
        } catch (AuthorNotFoundException $e) {
            return ApiResponseFactory::notFound($e->getMessage());
        } catch (RewardNotFoundException $e) {
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
     * Delete an author-reward link by id.
     *
     * DELETE /api/author-rewards/{id}
     */
    #[Route('/api/author-rewards/{id<\d+>}', name: 'api_author_rewards_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->authorRewardService->deleteById($id);

            return ApiResponseFactory::success(
                data: null,
                message: 'Author reward link deleted successfully.'
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
