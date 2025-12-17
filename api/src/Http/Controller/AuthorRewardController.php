<?php

namespace App\Http\Controller;

use App\Domain\Service\AuthorRewardService;
use App\Http\Request\AuthorReward\AssignAuthorRewardRequest;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\AuthorRewardResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthorRewardController extends AbstractController
{
    public function __construct(
        private readonly AuthorRewardService $authorRewardService,
        private readonly AuthorRewardResponse $authorRewardResponse,
    ) {
    }

    #[Route('/api/authors/{authorId<\d+>}/rewards', name: 'api_author_rewards_list', methods: ['GET'])]
    public function listForAuthor(int $authorId): JsonResponse
    {
        $authorRewards = $this->authorRewardService->listForAuthor($authorId);

        return ApiResponseFactory::success(
            data: $this->authorRewardResponse->collection($authorRewards),
            message: 'Author rewards list retrieved.'
        );
    }

    #[Route('/api/authors/{authorId<\d+>}/rewards', name: 'api_author_rewards_assign', methods: ['POST'])]
    public function assign(int $authorId, Request $request): JsonResponse
    {
        $dto = AssignAuthorRewardRequest::fromHttpRequest($request);

        $authorReward = $this->authorRewardService->assign(
            authorId: $authorId,
            rewardCode: $dto->getRewardCode()
        );

        return ApiResponseFactory::success(
            data: $this->authorRewardResponse->item($authorReward),
            message: 'Reward assigned successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    #[Route('/api/author-rewards/{id<\d+>}', name: 'api_author_rewards_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->authorRewardService->deleteById($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'Author reward link deleted successfully.'
        );
    }
}
