<?php

namespace App\Domain\Service;

use App\Domain\Entity\RefreshToken;
use App\Domain\Entity\User;
use App\Domain\Repository\RefreshTokenRepositoryInterface;

final class RefreshTokenService
{
    private const DEFAULT_TTL = 'P30D';

    public function __construct(
        private RefreshTokenRepositoryInterface $refreshTokenRepository
    ) {
    }

    public function issueForUser(User $user): RefreshToken
    {
        $token = $this->generateToken();
        while ($this->refreshTokenRepository->findOneByToken($token) !== null) {
            $token = $this->generateToken();
        }

        $expiresAt = (new \DateTimeImmutable())->add(new \DateInterval(self::DEFAULT_TTL));
        $refreshToken = new RefreshToken($user, $token, $expiresAt);
        $this->refreshTokenRepository->save($refreshToken);

        return $refreshToken;
    }

    public function rotate(RefreshToken $refreshToken): RefreshToken
    {
        $refreshToken->revoke();
        $this->refreshTokenRepository->save($refreshToken);

        return $this->issueForUser($refreshToken->getUser());
    }

    private function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(48)), '+/', '-_'), '=');
    }
}
