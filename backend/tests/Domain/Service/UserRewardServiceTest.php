<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service;

use App\Domain\Entity\Reward;
use App\Domain\Entity\User;
use App\Domain\Entity\UserReward;
use App\Domain\Exception\NotFound\RewardNotFoundException;
use App\Domain\Exception\NotFound\UserNotFoundException;
use App\Domain\Exception\NotFound\UserRewardNotFoundException;
use App\Domain\Repository\RewardRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Repository\UserRewardRepositoryInterface;
use App\Domain\Service\UserRewardService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserRewardServiceTest extends TestCase
{
    private UserRepositoryInterface&MockObject $userRepository;
    private RewardRepositoryInterface&MockObject $rewardRepository;
    private UserRewardRepositoryInterface&MockObject $userRewardRepository;
    private UserRewardService $service;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->rewardRepository = $this->createMock(RewardRepositoryInterface::class);
        $this->userRewardRepository = $this->createMock(UserRewardRepositoryInterface::class);
        $this->service = new UserRewardService(
            $this->userRepository,
            $this->rewardRepository,
            $this->userRewardRepository
        );
    }

    /**
     * 🎯 assign reward once and return existing link if already present.
     */
    public function test_assign_returns_existing_or_creates(): void
    {
        ## --------| Arrange |-------- ##
        $user = (new User())
            ->setEmail('user@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);
        $this->setId($user, 5);

        $reward = (new Reward())
            ->setCode('RWD')
            ->setLabel('Reward');
        $this->setId($reward, 9);

        $existing = (new UserReward())
            ->setUser($user)
            ->setReward($reward);

        $this->userRepository->method('getById')->with(5)->willReturn($user);
        $this->rewardRepository->method('findOneByCode')->with('RWD')->willReturn($reward);

        // First: existing found
        $this->userRewardRepository->method('findOneByUserAndReward')->willReturn($existing);
        $this->userRewardRepository->expects($this->never())->method('save');
        $returnedExisting = $this->service->assign(5, 'RWD');
        $this->assertSame($existing, $returnedExisting);

        // Second: none found, should create
        $this->userRewardRepository = $this->createMock(UserRewardRepositoryInterface::class);
        $this->service = new UserRewardService(
            $this->userRepository,
            $this->rewardRepository,
            $this->userRewardRepository
        );
        $this->userRewardRepository->method('findOneByUserAndReward')->willReturn(null);
        $captured = null;
        $this->userRewardRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(static function (UserReward $userReward) use (&$captured): void {
                $captured = $userReward;
            });

        $created = $this->service->assign(5, 'RWD');
        $this->assertSame($captured, $created);
        $this->assertSame($user, $created->getUser());
        $this->assertSame($reward, $created->getReward());
    }

    /**
     * 🎯 throw when assigning to missing user or reward.
     */
    public function test_assign_throws_for_missing_user_or_reward(): void
    {
        ## --------| Arrange |-------- ##
        $this->userRepository->method('getById')->with(404)->willReturn(null);

        ## --------| Act |-------- ##
        $this->expectException(UserNotFoundException::class);
        $this->service->assign(404, 'X');
    }

    /**
     * 🎯 throw when reward code is unknown.
     */
    public function test_assign_throws_for_missing_reward(): void
    {
        ## --------| Arrange |-------- ##
        $user = (new User())
            ->setEmail('user@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);
        $this->setId($user, 6);

        $this->userRepository->method('getById')->with(6)->willReturn($user);
        $this->rewardRepository->method('findOneByCode')->with('MISS')->willReturn(null);

        ## --------| Act |-------- ##
        $this->expectException(RewardNotFoundException::class);
        $this->service->assign(6, 'MISS');
    }

    /**
     * 🎯 getOrFail throws when userReward is missing.
     */
    public function test_get_or_fail_throws_when_missing(): void
    {
        ## --------| Arrange |-------- ##
        $this->userRewardRepository->method('getById')->with(404)->willReturn(null);

        ## --------| Act |-------- ##
        $this->expectException(UserRewardNotFoundException::class);
        $this->service->getOrFail(404);
    }

    /**
     * 🎯 deleteById removes the association after fetching it.
     */
    public function test_delete_by_id_fetches_and_deletes(): void
    {
        ## --------| Arrange |-------- ##
        $userReward = new UserReward();
        $this->userRewardRepository->method('getById')->with(7)->willReturn($userReward);
        $this->userRewardRepository->expects($this->once())->method('delete')->with($userReward);

        ## --------| Act |-------- ##
        $this->service->deleteById(7);

        ## --------| Assert |-------- ##
        $this->addToAssertionCount(1);
    }

    private function setId(object $entity, int $id): void
    {
        $ref = new \ReflectionClass($entity);
        if ($ref->hasProperty('id')) {
            $prop = $ref->getProperty('id');
            $prop->setAccessible(true);
            $prop->setValue($entity, $id);
        }
    }
}
