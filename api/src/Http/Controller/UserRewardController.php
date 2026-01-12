<?php

namespace App\Http\Controller;

use App\Domain\Service\UserRewardService;
use App\Http\Request\UserReward\AssignUserRewardRequest;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\UserRewardResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserRewardController extends AbstractController
{
    public function __construct(
        private readonly UserRewardService $userRewardService,
        private readonly UserRewardResponse $userRewardResponse,
    ) {
    }

    #[Route('/api/users/{userId<\d+>}/rewards', name: 'api_user_rewards_list', methods: ['GET'])]
    public function listForUser(int $userId): JsonResponse
    {
        $this->denyAccessUnlessGranted('USER_REWARD_LIST', $userId);
        $userRewards = $this->userRewardService->listForUser($userId);

        return ApiResponseFactory::success(
            data: $this->userRewardResponse->collection($userRewards),
            message: 'User rewards list retrieved.'
        );
    }

    #[Route('/api/users/{userId<\d+>}/rewards', name: 'api_user_rewards_assign', methods: ['POST'])]
    public function assign(int $userId, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $dto = AssignUserRewardRequest::fromHttpRequest($request);

        $userReward = $this->userRewardService->assign(
            userId: $userId,
            rewardCode: $dto->getRewardCode()
        );

        return ApiResponseFactory::success(
            data: $this->userRewardResponse->item($userReward),
            message: 'Reward assigned successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    #[Route('/api/user-rewards/{id<\d+>}', name: 'api_user_rewards_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $userReward = $this->userRewardService->getOrFail($id);
        $this->denyAccessUnlessGranted('USER_REWARD_DELETE', $userReward);
        $this->userRewardService->deleteById($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'User reward link deleted successfully.'
        );
    }
}
