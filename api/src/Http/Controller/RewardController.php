<?php

namespace App\Http\Controller;

use App\Domain\Service\RewardService;
use App\Http\Request\Pagination;
use App\Http\Request\Reward\CreateRewardRequest;
use App\Http\Request\Reward\UpdateRewardRequest;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\RewardListResponse;
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
        private readonly RewardListResponse $rewardListResponse,
        private readonly RewardResponse $rewardResponse
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $pagination = Pagination::fromRequest(
            $request,
            allowedSorts: ['id' => 'id', 'code' => 'code', 'label' => 'label'],
            defaultSort: 'id',
            defaultDirection: 'ASC',
            defaultLimit: 100,
            maxLimit: 200
        );

        $total = $this->rewardService->countRewards();

        return ApiResponseFactory::success(
            data: $this->rewardListResponse->collection(
                $this->rewardService->listPage(
                    $pagination->getLimit(),
                    $pagination->getOffset(),
                    $pagination->getSort(),
                    $pagination->getDirection()
                )
            ),
            message: 'Rewards list retrieved.',
            meta: [
                'pagination' => [
                    'page' => $pagination->getPage(),
                    'limit' => $pagination->getLimit(),
                    'total' => $total,
                    'pages' => (int) ceil($total / max(1, $pagination->getLimit())),
                    'sort' => $pagination->getSort(),
                    'direction' => $pagination->getDirection(),
                ],
            ]
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
