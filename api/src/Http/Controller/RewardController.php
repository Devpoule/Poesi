<?php

namespace App\Http\Controller;

use App\Domain\Service\RewardService;
use App\Http\Request\Reward\CreateRewardRequest;
use App\Http\Request\Reward\UpdateRewardRequest;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\RewardResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/rewards', name: 'api_rewards_')]
final class RewardController extends AbstractController
{
    public function __construct(
        private readonly RewardService $rewardService,
        private readonly RewardResponse $rewardResponse
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return ApiResponseFactory::success(
            data: $this->rewardResponse->collection($this->rewardService->listAll()),
            message: 'Rewards list retrieved.'
        );
    }

    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $reward = $this->rewardService->getOrFail($id);

        return ApiResponseFactory::success(
            data: $this->rewardResponse->item($reward),
            message: 'Reward retrieved.'
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $dto = CreateRewardRequest::fromHttpRequest($request);

        $reward = $this->rewardService->create(
            code: $dto->getCode(),
            label: $dto->getLabel()
        );

        return ApiResponseFactory::success(
            data: $this->rewardResponse->item($reward),
            message: 'Reward created successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $dto = UpdateRewardRequest::fromHttpRequest($request);

        $reward = $this->rewardService->update(
            id: $id,
            code: $dto->getCode(),
            label: $dto->getLabel()
        );

        return ApiResponseFactory::success(
            data: $this->rewardResponse->item($reward),
            message: 'Reward updated successfully.'
        );
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->rewardService->delete($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'Reward deleted successfully.'
        );
    }
}
