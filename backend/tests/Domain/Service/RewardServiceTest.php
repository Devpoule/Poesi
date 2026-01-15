<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service;

use App\Domain\Entity\Reward;
use App\Domain\Exception\NotFound\RewardNotFoundException;
use App\Domain\Repository\RewardRepositoryInterface;
use App\Domain\Service\RewardService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RewardServiceTest extends TestCase
{
    private RewardRepositoryInterface&MockObject $repository;
    private RewardService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(RewardRepositoryInterface::class);
        $this->service = new RewardService($this->repository);
    }

    /**
     * 🎯 create reward and persist it.
     */
    public function test_create_persists_reward(): void
    {
        ## --------| Arrange |-------- ##
        $captured = null;
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(static function (Reward $reward) use (&$captured): void {
                $captured = $reward;
            });

        ## --------| Act |-------- ##
        $reward = $this->service->create('CODE', 'Label');

        ## --------| Assert |-------- ##
        $this->assertSame($captured, $reward);
        $this->assertSame('CODE', $reward->getCode());
        $this->assertSame('Label', $reward->getLabel());
    }

    /**
     * 🎯 getOrFail throws when not found.
     */
    public function test_get_or_fail_throws_when_missing(): void
    {
        ## --------| Arrange |-------- ##
        $this->repository->method('getById')->with(404)->willReturn(null);

        ## --------| Act |-------- ##
        $this->expectException(RewardNotFoundException::class);
        $this->service->getOrFail(404);
    }

    /**
     * 🎯 update modifies code and label then saves.
     */
    public function test_update_changes_fields(): void
    {
        ## --------| Arrange |-------- ##
        $reward = (new Reward())
            ->setCode('OLD')
            ->setLabel('Old');

        $this->repository->method('getById')->with(1)->willReturn($reward);
        $this->repository->expects($this->once())->method('save')->with($reward);

        ## --------| Act |-------- ##
        $updated = $this->service->update(1, 'NEW', 'New Label');

        ## --------| Assert |-------- ##
        $this->assertSame('NEW', $updated->getCode());
        $this->assertSame('New Label', $updated->getLabel());
    }
}
