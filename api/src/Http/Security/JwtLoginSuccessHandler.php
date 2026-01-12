<?php

namespace App\Http\Security;

use App\Domain\Entity\User;
use App\Domain\Service\RefreshTokenService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

final class JwtLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private RefreshTokenService $refreshTokenService,
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface || !$user instanceof User) {
            return new JsonResponse(['message' => 'Invalid user.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $jwt = $this->jwtManager->create($user);
        $refreshToken = $this->refreshTokenService->issueForUser($user);

        return new JsonResponse([
            'token' => $jwt,
            'refreshToken' => $refreshToken->getToken(),
            'refreshTokenExpiresAt' => $refreshToken->getExpiresAt()->format(\DateTimeInterface::ATOM),
        ]);
    }
}
