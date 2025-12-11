<?php

namespace App\Http\Controller;

use App\Domain\Service\UserService;
use App\Http\Mapper\UserMapper;
use App\Http\Response\ApiResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * HTTP API controller exposing basic CRUD for Users.
 *
 * This is infrastructure for authentication; you will likely
 * adapt it when wiring real security (hashing, JWT, etc.).
 */
#[Route('/api/users', name: 'api_users_')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserMapper $userMapper,
    ) {
    }

    /**
     * List all users.
     *
     * GET /api/users
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $users = $this->userService->listAll();

        return ApiResponseFactory::success(
            data: $this->userMapper->toCollection($users),
            message: 'Users list retrieved.'
        );
    }

    /**
     * Get one user by id.
     *
     * GET /api/users/{id}
     */
    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->getUserOrFail($id);

        return ApiResponseFactory::success(
            data: $this->userMapper->toArray($user),
            message: 'User retrieved.'
        );
    }

    /**
     * Create a user.
     *
     * ⚠ For now, this endpoint expects a password that is already hashed.
     * You will adapt this when integrating security / password hashing.
     *
     * Example JSON body:
     * {
     *   "email": "user@example.com",
     *   "passwordHash": "bcrypt_or_argon_hash_here",
     *   "roles": ["ROLE_USER"]
     * }
     *
     * POST /api/users
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return ApiResponseFactory::validationError(
                message: 'Invalid JSON payload.',
                errors: ['body' => ['Request body must be valid JSON.']]
            );
        }

        $email        = $payload['email'] ?? null;
        $passwordHash = $payload['passwordHash'] ?? null;
        $roles        = $payload['roles'] ?? ['ROLE_USER'];

        $errors = [];

        if ($email === null || trim((string) $email) === '') {
            $errors['email'][] = 'email is required.';
        }

        if ($passwordHash === null || trim((string) $passwordHash) === '') {
            $errors['passwordHash'][] = 'passwordHash is required.';
        }

        if (!empty($errors)) {
            return ApiResponseFactory::validationError(
                message: 'Invalid user payload.',
                errors: $errors
            );
        }

        if (!is_array($roles)) {
            $roles = ['ROLE_USER'];
        }

        $user = $this->userService->createUser(
            email: (string) $email,
            hashedPassword: (string) $passwordHash,
            roles: $roles
        );

        return ApiResponseFactory::success(
            data: $this->userMapper->toArray($user),
            message: 'User created successfully.',
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * Update a user.
     *
     * Example JSON body:
     * {
     *   "email": "new@example.com",
     *   "passwordHash": "new_hash",
     *   "roles": ["ROLE_USER", "ROLE_ADMIN"]
     * }
     *
     * PUT /api/users/{id}
     */
    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return ApiResponseFactory::validationError(
                message: 'Invalid JSON payload.',
                errors: ['body' => ['Request body must be valid JSON.']]
            );
        }

        $email        = $payload['email'] ?? null;
        $passwordHash = $payload['passwordHash'] ?? null;
        $roles        = $payload['roles'] ?? null;

        if ($roles !== null && !is_array($roles)) {
            $roles = null;
        }

        $user = $this->userService->updateUser(
            userId: $id,
            email: $email !== null ? (string) $email : null,
            hashedPassword: $passwordHash !== null ? (string) $passwordHash : null,
            roles: $roles
        );

        return ApiResponseFactory::success(
            data: $this->userMapper->toArray($user),
            message: 'User updated successfully.'
        );
    }

    /**
     * Delete a user.
     *
     * DELETE /api/users/{id}
     */
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
