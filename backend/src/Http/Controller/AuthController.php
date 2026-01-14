<?php

namespace App\Http\Controller;

use App\Domain\Service\RefreshTokenService;
use App\Domain\Repository\RefreshTokenRepositoryInterface;
use App\Http\Request\JsonRequestDecoder;
use App\Http\Request\RequestPayload;
use App\Http\Response\ApiResponseFactory;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_auth_')]
final class AuthController extends AbstractController
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private RefreshTokenRepositoryInterface $refreshTokenRepository,
        private RefreshTokenService $refreshTokenService,
    ) {
    }

    #[Route('/token/refresh', name: 'refresh_token', methods: ['POST'])]
    public function refreshToken(Request $request): JsonResponse
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $token = RequestPayload::getTrimmedString($payload, 'refreshToken');

        if ($token === null) {
            return ApiResponseFactory::validationError(
                message: 'Invalid refresh token payload.',
                errors: ['refreshToken' => ['refreshToken is required.']]
            );
        }

        $refreshToken = $this->refreshTokenRepository->findOneByToken($token);
        if ($refreshToken === null || $refreshToken->isExpired() || $refreshToken->isRevoked()) {
            return ApiResponseFactory::error(
                message: 'Invalid refresh token.',
                code: 'UNAUTHORIZED',
                type: 'error',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_UNAUTHORIZED
            );
        }

        $user = $refreshToken->getUser();
        if ($user === null) {
            return ApiResponseFactory::error(
                message: 'Invalid refresh token.',
                code: 'UNAUTHORIZED',
                type: 'error',
                errors: null,
                data: null,
                httpStatus: Response::HTTP_UNAUTHORIZED
            );
        }

        $newRefreshToken = $this->refreshTokenService->rotate($refreshToken);
        $jwt = $this->jwtManager->create($user);

        return ApiResponseFactory::success(
            data: [
                'token' => $jwt,
                'refreshToken' => $newRefreshToken->getToken(),
                'refreshTokenExpiresAt' => $newRefreshToken->getExpiresAt()->format(\DateTimeInterface::ATOM),
            ],
            message: 'Token refreshed.'
        );
    }
}
