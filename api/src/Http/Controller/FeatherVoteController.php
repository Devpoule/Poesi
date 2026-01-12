<?php

namespace App\Http\Controller;

use App\Domain\Exception\CannotVote\CannotVoteOwnPoemException;
use App\Domain\Service\FeatherVoteService;
use App\Domain\Entity\User;
use App\Http\Request\Pagination;
use App\Http\Request\FeatherVote\CreateFeatherVoteRequest;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\FeatherVoteListResponse;
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
        private readonly FeatherVoteListResponse $featherVoteListResponse,
        private readonly FeatherVoteResponse $featherVoteResponse,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $pagination = Pagination::fromRequest(
            $request,
            allowedSorts: ['id' => 'id', 'createdAt' => 'createdAt', 'updatedAt' => 'updatedAt'],
            defaultSort: 'updatedAt',
            defaultDirection: 'DESC',
            defaultLimit: 50,
            maxLimit: 200
        );

        $total = $this->featherVoteService->countVotes();

        return ApiResponseFactory::success(
            data: $this->featherVoteListResponse->collection(
                $this->featherVoteService->listPage(
                    $pagination->getLimit(),
                    $pagination->getOffset(),
                    $pagination->getSort(),
                    $pagination->getDirection()
                )
            ),
            message: 'Feather votes list retrieved.',
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
        $vote = $this->featherVoteService->getVoteOrFail($id);
        $this->denyAccessUnlessGranted('VOTE_VIEW', $vote);

        return ApiResponseFactory::success(
            data: $this->featherVoteResponse->item($vote),
            message: 'Feather vote retrieved.'
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $dto = CreateFeatherVoteRequest::fromHttpRequest($request);
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            return ApiResponseFactory::error(
                message: 'Authentication required.',
                code: 'UNAUTHORIZED',
                type: 'error',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_UNAUTHORIZED
            );
        }
        if ($dto->getVoterUserId() !== null && $dto->getVoterUserId() !== $currentUser->getId()) {
            return ApiResponseFactory::error(
                message: 'Cannot vote as another user.',
                code: 'FORBIDDEN',
                type: 'error',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_FORBIDDEN
            );
        }

        try {
            $result = $this->featherVoteService->castVote(
                voterUserId: (int) $currentUser->getId(),
                poemId: $dto->getPoemId(),
                featherType: $dto->getFeatherType()
            );
        } catch (CannotVoteOwnPoemException $e) {
            return ApiResponseFactory::error(
                message: $e->getMessage(),
                code: $e->getErrorCode(),
                type: 'warning',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_CONFLICT
            );
        }

        return ApiResponseFactory::success(
            data: $this->featherVoteResponse->item($result['vote']),
            message: $result['created'] ? 'Feather vote created successfully.' : 'Feather vote updated successfully.',
            meta: ['created' => (bool) $result['created']],
            httpStatus: $result['created'] ? Response::HTTP_CREATED : Response::HTTP_OK
        );
    }

    #[Route('/poem/{poemId<\d+>}', name: 'list_for_poem', methods: ['GET'])]
    public function listForPoem(int $poemId, Request $request): JsonResponse
    {
        $pagination = Pagination::fromRequest(
            $request,
            allowedSorts: ['id' => 'id', 'createdAt' => 'createdAt', 'updatedAt' => 'updatedAt'],
            defaultSort: 'updatedAt',
            defaultDirection: 'DESC',
            defaultLimit: 50,
            maxLimit: 200
        );

        $total = $this->featherVoteService->countVotesForPoem($poemId);

        return ApiResponseFactory::success(
            data: $this->featherVoteListResponse->collection(
                $this->featherVoteService->listVotesForPoemPage(
                    $poemId,
                    $pagination->getLimit(),
                    $pagination->getOffset(),
                    $pagination->getSort(),
                    $pagination->getDirection()
                )
            ),
            message: 'Feather votes for poem retrieved.',
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

    #[Route('/user/{userId<\d+>}', name: 'list_for_user', methods: ['GET'])]
    public function listForUser(int $userId, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('VOTE_LIST', $userId);
        $pagination = Pagination::fromRequest(
            $request,
            allowedSorts: ['id' => 'id', 'createdAt' => 'createdAt', 'updatedAt' => 'updatedAt'],
            defaultSort: 'updatedAt',
            defaultDirection: 'DESC',
            defaultLimit: 50,
            maxLimit: 200
        );

        $total = $this->featherVoteService->countVotesByUser($userId);

        return ApiResponseFactory::success(
            data: $this->featherVoteListResponse->collection(
                $this->featherVoteService->listVotesByUserPage(
                    $userId,
                    $pagination->getLimit(),
                    $pagination->getOffset(),
                    $pagination->getSort(),
                    $pagination->getDirection()
                )
            ),
            message: 'Feather votes for user retrieved.',
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

    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $vote = $this->featherVoteService->getVoteOrFail($id);
        $this->denyAccessUnlessGranted('VOTE_DELETE', $vote);
        $this->featherVoteService->deleteVote($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'Feather vote deleted successfully.'
        );
    }
}
