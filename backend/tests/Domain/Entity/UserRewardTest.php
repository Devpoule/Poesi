<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Reward;
use App\Domain\Entity\User;
use App\Domain\Entity\UserReward;
use PHPUnit\Framework\TestCase;

/**
 * 🎯 Ensure user-reward link stores user, reward, and timestamp.
 */
class UserRewardTest extends TestCase
{
    public function test_link_is_created_with_timestamp(): void
    {
        ## ───────| Arrange |─────── ##
        $user = (new User())
            ->setEmail('reward@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER']);

        $reward = (new Reward())
            ->setCode('milestone')
            ->setLabel('Milestone');

        ## ───────| Act |─────── ##
        $link = (new UserReward())
            ->setUser($user)
            ->setReward($reward);

        ## ───────| Assert |─────── ##
        $this->assertSame($user, $link->getUser());
        $this->assertSame($reward, $link->getReward());
        $this->assertInstanceOf(\DateTimeImmutable::class, $link->getCreatedAt());
    }

    public function test_reward_relation_is_removed_symmetrically(): void
    {
        ## ───────| Arrange |─────── ##
        $user = (new User())
            ->setEmail('reward2@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER']);

        $reward = (new Reward())
            ->setCode('bonus')
            ->setLabel('Bonus');

        $link = (new UserReward())
            ->setUser($user)
            ->setReward($reward);

        ## ───────| Act |─────── ##
        $link->setUser(null);
        $link->setReward(null);

        ## ───────| Assert |─────── ##
        $this->assertNull($link->getUser());
        $this->assertNull($link->getReward());
    }
}
