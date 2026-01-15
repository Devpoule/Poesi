<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Reward;
use App\Domain\Entity\UserReward;
use PHPUnit\Framework\TestCase;

/**
 * 🎯 Ensure reward catalog data and relations behave.
 */
class RewardTest extends TestCase
{
    public function test_reward_fields_and_relation_collection(): void
    {
        ## ───────| Arrange |─────── ##
        $reward = (new Reward())
            ->setCode('early_bird')
            ->setLabel('Early Bird');

        ## ───────| Assert |─────── ##
        $this->assertSame('EARLY_BIRD', $reward->getCode()); // uppercased by setter
        $this->assertSame('Early Bird', $reward->getLabel());
        $this->assertCount(0, $reward->getUserRewards());
    }
}
