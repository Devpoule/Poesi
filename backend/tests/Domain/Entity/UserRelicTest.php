<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\User;
use App\Domain\Entity\UserRelic;
use PHPUnit\Framework\TestCase;

/**
 * 🎯 Ensure user-relic link keeps ownership and rarity context.
 */
class UserRelicTest extends TestCase
{
    public function test_user_relic_link_fields(): void
    {
        ## ───────| Arrange |─────── ##
        $user = (new User())
            ->setEmail('relic@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER']);

        ## ───────| Act |─────── ##
        $link = new UserRelic(
            user: $user,
            relicKey: 'phoenix',
            reason: 'Editorial pick',
            context: ['poem_id' => 42]
        );

        ## ───────| Assert |─────── ##
        $this->assertSame($user, $link->getUser());
        $this->assertSame('phoenix', $link->getRelicKey());
        $this->assertSame('Editorial pick', $link->getReason());
        $this->assertSame(['poem_id' => 42], $link->getContext());
        $this->assertInstanceOf(\DateTimeImmutable::class, $link->getGrantedAt());
    }
}
