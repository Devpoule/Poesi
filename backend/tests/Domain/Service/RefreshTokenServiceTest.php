<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service;

use App\Domain\Entity\RefreshToken;
use App\Domain\Entity\User;
use App\Domain\Repository\RefreshTokenRepositoryInterface;
use App\Domain\Service\RefreshTokenService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RefreshTokenServiceTest extends TestCase
{
    private RefreshTokenRepositoryInterface&MockObject $repository;
    private RefreshTokenService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(RefreshTokenRepositoryInterface::class);
        $this->service = new RefreshTokenService($this->repository);
    }

    /**
     * 🎯 issue a token for a user with future expiry and persistence.
     */
    public function test_issue_for_user_persists_refresh_token(): void
    {
        ## --------| Arrange |-------- ##
        $user = (new User())
            ->setEmail('user@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);

        $this->repository->method('findOneByToken')->willReturn(null);
        $captured = null;
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(static function (RefreshToken $token) use (&$captured): void {
                $captured = $token;
            });

        ## --------| Act |-------- ##
        $refreshToken = $this->service->issueForUser($user);

        ## --------| Assert |-------- ##
        $this->assertSame($captured, $refreshToken);
        $this->assertSame($user, $refreshToken->getUser());
        $this->assertNotEmpty($refreshToken->getToken());
        $this->assertTrue($refreshToken->getExpiresAt() > new \DateTimeImmutable('+29 days'));
    }

    /**
     * 🎯 issue retries until a unique token is generated.
     */
    public function test_issue_retries_on_collision(): void
    {
        ## --------| Arrange |-------- ##
        $user = (new User())
            ->setEmail('retry@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);

        $collisionToken = new RefreshToken($user, 'dup', new \DateTimeImmutable('+1 day'));
        $this->repository->method('findOneByToken')->willReturnOnConsecutiveCalls($collisionToken, null);
        $this->repository->expects($this->once())->method('save');

        ## --------| Act |-------- ##
        $refreshToken = $this->service->issueForUser($user);

        ## --------| Assert |-------- ##
        $this->assertNotSame('dup', $refreshToken->getToken());
    }

    /**
     * 🎯 rotate revokes old token and issues a new one.
     */
    public function test_rotate_revokes_and_issues_new_token(): void
    {
        ## --------| Arrange |-------- ##
        $user = (new User())
            ->setEmail('rotate@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);
        $old = new RefreshToken($user, 'old', new \DateTimeImmutable('+1 day'));

        $this->repository->method('findOneByToken')->willReturn(null);
        $this->repository->expects($this->exactly(2))->method('save');

        ## --------| Act |-------- ##
        $new = $this->service->rotate($old);

        ## --------| Assert |-------- ##
        $this->assertTrue($old->isRevoked());
        $this->assertNotSame($old->getToken(), $new->getToken());
        $this->assertSame($user, $new->getUser());
    }
}
