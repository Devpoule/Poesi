<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\RefreshToken;
use App\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * 🎯 Verify refresh token lifecycle (expiry, revoke).
 */
class RefreshTokenTest extends TestCase
{
    public function test_expiry_and_revoke_states(): void
    {
        ## ───────| Arrange |─────── ##
        $user = (new User())
            ->setEmail('token@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER']);
        $future = new \DateTimeImmutable('+1 day');
        $token = new RefreshToken($user, 'token-value', $future);

        ## ───────| Act |─────── ##
        $isExpiredBefore = $token->isExpired();
        $token->revoke();
        $isExpiredAfter = $token->isExpired();

        ## ───────| Assert |─────── ##
        $this->assertFalse($isExpiredBefore);
        $this->assertTrue($token->isRevoked());
        $this->assertInstanceOf(\DateTimeImmutable::class, $token->getRevokedAt());
        $this->assertFalse($isExpiredAfter); // still in the future even if revoked
    }

    /**
     * 🎯 Expired tokens should report expired even if not revoked.
     */
    public function test_expired_token_reports_expired(): void
    {
        ## ───────| Arrange |─────── ##
        $user = (new User())
            ->setEmail('token2@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER']);
        $past = new \DateTimeImmutable('-1 hour');
        $token = new RefreshToken($user, 'token-expired', $past);

        ## ───────| Act |─────── ##
        $isExpired = $token->isExpired();

        ## ───────| Assert |─────── ##
        $this->assertTrue($isExpired);
        $this->assertFalse($token->isRevoked());
    }
}
