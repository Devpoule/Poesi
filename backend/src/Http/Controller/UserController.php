<?php

namespace App\Http\Controller;

use App\Domain\Service\UserService;
use App\Http\Request\User\CreateUserRequest;
use App\Http\Request\User\UpdateUserRequest;
use App\Http\Request\Pagination;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\UserAdminListResponse;
use App\Http\Response\UserOptionResponse;
use App\Http\Response\UserPublicResponse;
use App\Http\Response\UserResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/users', name: 'api_users_')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserResponse $userResponse,
        private readonly UserAdminListResponse $userAdminListResponse,
        private readonly UserOptionResponse $userOptionResponse,
        private readonly UserPublicResponse $userPublicResponse,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function list(Request $request): JsonResponse
    {
        $pagination = Pagination::fromRequest(
            $request,
            allowedSorts: ['id' => 'id', 'email' => 'email', 'createdAt' => 'createdAt'],
            defaultSort: 'createdAt',
            defaultDirection: 'DESC',
            defaultLimit: 50,
            maxLimit: 200
        );

        $total = $this->userService->countUsers();

        return ApiResponseFactory::success(
            data: $this->userAdminListResponse->collection(
                $this->userService->listAdminSummaryPage(
                    $pagination->getLimit(),
                    $pagination->getOffset(),
                    $pagination->getSort(),
                    $pagination->getDirection()
                )
            ),
            message: 'Users list retrieved.',
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

    #[Route('/options', name: 'options', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function options(Request $request): JsonResponse
    {
        $pagination = Pagination::fromRequest(
            $request,
            allowedSorts: ['id' => 'id', 'pseudo' => 'pseudo', 'email' => 'email'],
            defaultSort: 'id',
            defaultDirection: 'ASC',
            defaultLimit: 100,
            maxLimit: 200
        );

        $total = $this->userService->countUsers();

        return ApiResponseFactory::success(
            data: $this->userOptionResponse->collection(
                $this->userService->listOptionsPage(
                    $pagination->getLimit(),
                    $pagination->getOffset(),
                    $pagination->getSort(),
                    $pagination->getDirection()
                )
            ),
            message: 'User options retrieved.',
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

    #[Route('/public', name: 'public', methods: ['GET'])]
    public function publicList(Request $request): JsonResponse
    {
        $pagination = Pagination::fromRequest(
            $request,
            allowedSorts: ['id' => 'id', 'pseudo' => 'pseudo', 'moodColor' => 'moodColor'],
            defaultSort: 'id',
            defaultDirection: 'ASC',
            defaultLimit: 50,
            maxLimit: 200
        );

        $total = $this->userService->countUsers();

        return ApiResponseFactory::success(
            data: $this->userPublicResponse->collection(
                $this->userService->listPublicProfilesPage(
                    $pagination->getLimit(),
                    $pagination->getOffset(),
                    $pagination->getSort(),
                    $pagination->getDirection()
                )
            ),
            message: 'Public users list retrieved.',
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
        $user = $this->userService->getUserOrFail($id);
        $this->denyAccessUnlessGranted('USER_VIEW', $user);

        return ApiResponseFactory::success(
            data: $this->userResponse->item($user),
            message: 'User retrieved.'
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $dto = CreateUserRequest::fromHttpRequest($request);
        $passwordHash = $dto->getPasswordHash();
        if ($passwordHash === null && $dto->getPassword() !== null) {
            $passwordHash = password_hash($dto->getPassword(), PASSWORD_BCRYPT);
        }

        $user = $this->userService->createUser(
            email: $dto->getEmail(),
            hashedPassword: (string) $passwordHash,
            roles: $dto->getRoles(),
            pseudo: $dto->getPseudo(),
            moodColor: $dto->getMoodColor(),
            totemId: $dto->getTotemId(),
            totemKey: $dto->getTotemKey()
        );

        return ApiResponseFactory::success(
            data: $this->userResponse->item($user),
            message: 'User created successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $dto = UpdateUserRequest::fromHttpRequest($request);
        $user = $this->userService->getUserOrFail($id);
        $this->denyAccessUnlessGranted('USER_EDIT', $user);
        $passwordHash = $dto->getPasswordHash();
        if ($passwordHash === null && $dto->getPassword() !== null) {
            $passwordHash = password_hash($dto->getPassword(), PASSWORD_BCRYPT);
        }

        $user = $this->userService->updateUser(
            userId: $id,
            email: $dto->getEmail(),
            hashedPassword: $passwordHash,
            roles: $dto->getRoles(),
            pseudo: $dto->getPseudo(),
            moodColor: $dto->getMoodColor(),
            totemId: $dto->getTotemId(),
            totemKey: $dto->getTotemKey()
        );

        return ApiResponseFactory::success(
            data: $this->userResponse->item($user),
            message: 'User updated successfully.'
        );
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $user = $this->userService->getUserOrFail($id);
        $this->denyAccessUnlessGranted('USER_DELETE', $user);
        $this->userService->deleteUser($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'User deleted successfully.'
        );
    }
}
