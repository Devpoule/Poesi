<?php

namespace App\Http\Controller;

use App\Domain\Service\RewardService;
use App\Http\Mapper\RewardMapper;
use App\Http\Response\ApiResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * HTTP API controller handling Reward catalog
 * and assignment of rewards to authors.
 */
class RewardController extends AbstractController
{
    public function __construct(
        private readonly RewardService $rewardService,
        private readonly RewardMapper $rewardMapper,
    ) {
    }

    /**
     * List all rewards.
     *
     * GET /api/rewards
     */
    #[Route('/api/rewards', name: 'api_rewards_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $rewards = $this->rewardService->listAllRewards();

        return ApiResponseFactory::success(
            data: $this->rewardMapper->toCollection($rewards),
            message: 'Rewards list retrieved.'
        );
    }

    /**
     * Get a single reward by id.
     *
     * GET /api/rewards/{id}
     */
    #[Route('/api/rewards/{id<\d+>}', name: 'api_rewards_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $reward = $this->rewardService->getRewardOrFail($id);

        return ApiResponseFactory::success(
            data: $this->rewardMapper->toArray($reward),
            message: 'Reward retrieved.'
        );
    }

    /**
     * Create a new reward.
     *
     * Expected JSON body:
     * {
     *   "code": "FIRST_POEM",
     *   "label": "First poem published"
     * }
     *
     * POST /api/rewards
     */
    #[Route('/api/rewards', name: 'api_rewards_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return ApiResponseFactory::validationError(
                message: 'Invalid JSON payload.',
                errors: ['body' => ['Request body must be valid JSON.']]
            );
        }

        $code  = $payload['code'] ?? null;
        $label = $payload['label'] ?? null;

        $errors = [];

        if ($code === null || trim((string) $code) === '') {
            $errors['code'][] = 'code is required.';
        }

        if ($label === null || trim((string) $label) === '') {
            $errors['label'][] = 'label is required.';
        }

        if (!empty($errors)) {
            return ApiResponseFactory::validationError(
                message: 'Invalid reward payload.',
                errors: $errors
            );
        }

        $reward = $this->rewardService->createReward(
            code: (string) $code,
            label: (string) $label
        );

        return ApiResponseFactory::success(
            data: $this->rewardMapper->toArray($reward),
            message: 'Reward created successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * Update an existing reward.
     *
     * Example JSON body:
     * {
     *   "code": "UPDATED_CODE",
     *   "label": "New label"
     * }
     *
     * PUT /api/rewards/{id}
     */
    #[Route('/api/rewards/{id<\d+>}', name: 'api_rewards_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return ApiResponseFactory::validationError(
                message: 'Invalid JSON payload.',
                errors: ['body' => ['Request body must be valid JSON.']]
            );
        }

        $code  = $payload['code'] ?? null;
        $label = $payload['label'] ?? null;

        $reward = $this->rewardService->updateReward(
            rewardId: $id,
            code: $code !== null ? (string) $code : null,
            label: $label !== null ? (string) $label : null
        );

        return ApiResponseFactory::success(
            data: $this->rewardMapper->toArray($reward),
            message: 'Reward updated successfully.'
        );
    }

    /**
     * Delete a reward.
     *
     * DELETE /api/rewards/{id}
     */
    #[Route('/api/rewards/{id<\d+>}', name: 'api_rewards_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->rewardService->deleteReward($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'Reward deleted successfully.'
        );
    }

    /**
     * List all rewards assigned to an author.
     *
     * GET /api/authors/{authorId}/rewards
     */
    #[Route('/api/authors/{authorId<\d+>}/rewards', name: 'api_author_rewards_list', methods: ['GET'])]
    public function listForAuthor(int $authorId): JsonResponse
    {
        $authorRewards = $this->rewardService->listRewardsForAuthor($authorId);

        // Simple inline mapping for AuthorReward
        $mapped = [];
        foreach ($authorRewards as $authorReward) {
            $reward = $authorReward->getReward();
            $author = $authorReward->getAuthor();

            $mapped[] = [
                'id'         => $authorReward->getId(),
                'earnedAt'   => $authorReward->getEarnedAt()->format(\DATE_ATOM),
                'author'     => [
                    'id'     => $author?->getId(),
                    'pseudo' => $author?->getPseudo(),
                    'email'  => $author?->getEmail(),
                ],
                'reward'     => $reward !== null ? [
                    'id'    => $reward->getId(),
                    'code'  => $reward->getCode(),
                    'label' => $reward->getLabel(),
                ] : null,
            ];
        }

        return ApiResponseFactory::success(
            data: $mapped,
            message: 'Rewards for author retrieved.'
        );
    }

    /**
     * Assign a reward (identified by its code) to an author.
     *
     * Expected JSON body:
     * {
     *   "rewardCode": "FIRST_POEM"
     * }
     *
     * POST /api/authors/{authorId}/rewards
     */
    #[Route('/api/authors/{authorId<\d+>}/rewards', name: 'api_author_rewards_assign', methods: ['POST'])]
    public function assignToAuthor(int $authorId, Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return ApiResponseFactory::validationError(
                message: 'Invalid JSON payload.',
                errors: ['body' => ['Request body must be valid JSON.']]
            );
        }

        $rewardCode = $payload['rewardCode'] ?? null;

        if ($rewardCode === null || trim((string) $rewardCode) === '') {
            return ApiResponseFactory::validationError(
                message: 'Invalid reward assignment payload.',
                errors: ['rewardCode' => ['rewardCode is required.']]
            );
        }

        $authorReward = $this->rewardService->assignRewardToAuthor(
            authorId: $authorId,
            rewardCode: (string) $rewardCode
        );

        $reward = $authorReward->getReward();
        $author = $authorReward->getAuthor();

        $data = [
            'id'       => $authorReward->getId(),
            'earnedAt' => $authorReward->getEarnedAt()->format(\DATE_ATOM),
            'author'   => [
                'id'     => $author?->getId(),
                'pseudo' => $author?->getPseudo(),
                'email'  => $author?->getEmail(),
            ],
            'reward'   => $reward !== null ? [
                'id'    => $reward->getId(),
                'code'  => $reward->getCode(),
                'label' => $reward->getLabel(),
            ] : null,
        ];

        return ApiResponseFactory::success(
            data: $data,
            message: 'Reward assigned to author successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * Delete an AuthorReward link.
     *
     * DELETE /api/author-rewards/{id}
     */
    #[Route('/api/author-rewards/{id<\d+>}', name: 'api_author_rewards_delete', methods: ['DELETE'])]
    public function deleteAuthorReward(int $id): JsonResponse
    {
        $this->rewardService->deleteAuthorReward($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'Author reward deleted successfully.'
        );
    }
}
