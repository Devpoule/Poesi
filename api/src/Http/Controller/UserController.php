<?php

namespace App\Http\Controller;

use App\Domain\Service\UserService;
use App\Http\Request\User\CreateUserRequest;
use App\Http\Request\User\UpdateUserRequest;
use App\Http\Response\ApiResponseFactory;
use App\Http\Response\UserResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return ApiResponseFactory::success(
            data: $this->userResponse->collection($this->userService->listAll()),
            message: 'Users list retrieved.'
        );
    }

    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->getUserOrFail($id);

        return ApiResponseFactory::success(
            data: $this->userResponse->item($user),
            message: 'User retrieved.'
        );
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $dto = CreateUserRequest::fromHttpRequest($request);

        $user = $this->userService->createUser(
            email: $dto->getEmail(),
            hashedPassword: $dto->getPasswordHash(),
            roles: $dto->getRoles()
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

        $user = $this->userService->updateUser(
            userId: $id,
            email: $dto->getEmail(),
            hashedPassword: $dto->getPasswordHash(),
            roles: $dto->getRoles()
        );

        return ApiResponseFactory::success(
            data: $this->userResponse->item($user),
            message: 'User updated successfully.'
        );
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->userService->deleteUser($id);

        return ApiResponseFactory::success(
            data: null,
            message: 'User deleted successfully.'
        );
    }
}
