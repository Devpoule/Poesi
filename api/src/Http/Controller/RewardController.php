<?php

namespace App\Http\Controller;

use App\Domain\Exception\NotFound\RewardNotFoundException;
use App\Domain\Service\RewardService;
use App\Http\Exception\ValidationException;
use App\Http\Mapper\RewardMapper;
use App\Http\Request\Reward\CreateRewardRequest;
use App\Http\Request\Reward\UpdateRewardRequest;
use App\Http\Response\ApiResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * HTTP API controller exposing CRUD operations for rewards.
 */
#[Route('/api/rewards', name: 'api_rewards_')]
final class RewardController extends AbstractController
{
    public function __construct(
        private readonly RewardService $rewardService,
        private readonly RewardMapper $rewardMapper
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return ApiResponseFactory::success(
            data: $this->rewardMapper->toCollection(
                $this->rewardService->listAll()
            ),
            message: 'Rewards list retrieved.'
        );
    }

    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $reward = $this->rewardService->getOrFail($id);

            return ApiResponseFactory::success(
                data: $this->rewardMapper->toArray($reward),
                message: 'Reward retrieved.'
            );
        } catch (RewardNotFoundException $e) {
            return ApiResponseFactory::notFound($e->getMessage());
        }
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(\Symfony\Component\HttpFoundation\Request $request): JsonResponse
    {
        try {
            $dto = CreateRewardRequest::fromHttpRequest($request);

            $reward = $this->rewardService->create(
                $dto->getCode(),
                $dto->getLabel()
            );

            return ApiResponseFactory::success(
                data: $this->rewardMapper->toArray($reward),
                message: 'Reward created successfully.',
                httpStatus: Response::HTTP_CREATED
            );
        } catch (ValidationException $e) {
            return ApiResponseFactory::validationError(
                $e->getMessage(),
                $e->getErrors(),
                $e->getErrorCode()
            );
        }
    }

    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, \Symfony\Component\HttpFoundation\Request $request): JsonResponse
    {
        try {
            $dto = UpdateRewardRequest::fromHttpRequest($request);

            $reward = $this->rewardService->update(
                id: $id,
                code: $dto->getCode(),
                label: $dto->getLabel()
            );

            return ApiResponseFactory::success(
                data: $this->rewardMapper->toArray($reward),
                message: 'Reward updated successfully.'
            );
        } catch (ValidationException $e) {
            return ApiResponseFactory::validationError(
                $e->getMessage(),
                $e->getErrors(),
                $e->getErrorCode()
            );
        } catch (RewardNotFoundException $e) {
            return ApiResponseFactory::notFound($e->getMessage());
        }
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->rewardService->delete($id);

            return ApiResponseFactory::success(
                data: null,
                message: 'Reward deleted successfully.'
            );
        } catch (RewardNotFoundException $e) {
            return ApiResponseFactory::notFound($e->getMessage());
        }
    }
}
