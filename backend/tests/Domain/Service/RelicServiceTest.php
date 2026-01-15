<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service;

use App\Domain\Entity\Relic;
use App\Domain\Exception\Conflict\RelicKeyAlreadyExistsException;
use App\Domain\Exception\NotFound\RelicNotFoundException;
use App\Domain\Repository\RelicRepositoryInterface;
use App\Domain\Service\RelicService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RelicServiceTest extends TestCase
{
    private RelicRepositoryInterface&MockObject $repository;
    private RelicService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(RelicRepositoryInterface::class);
        $this->service = new RelicService($this->repository);
    }

    /**
     * 🎯 create relic with unique key.
     */
    public function test_create_persists_relic(): void
    {
        ## --------| Arrange |-------- ##
        $this->repository->method('getByKey')->with('rare')->willReturn(null);

        $captured = null;
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(static function (Relic $relic) use (&$captured): void {
                $captured = $relic;
            });

        ## --------| Act |-------- ##
        $relic = $this->service->create('rare', 'Rare', 'epic', 'Desc', 'pic.png');

        ## --------| Assert |-------- ##
        $this->assertSame($captured, $relic);
        $this->assertSame('rare', $relic->getKey());
        $this->assertSame('Rare', $relic->getLabel());
        $this->assertSame('epic', $relic->getRarity());
        $this->assertSame('Desc', $relic->getDescription());
        $this->assertSame('pic.png', $relic->getPicture());
    }

    /**
     * 🎯 prevent duplicate relic key.
     */
    public function test_create_throws_when_key_exists(): void
    {
        ## --------| Arrange |-------- ##
        $this->repository->method('getByKey')->with('dup')->willReturn(new Relic());

        ## --------| Act |-------- ##
        $this->expectException(RelicKeyAlreadyExistsException::class);
        $this->service->create('dup', 'Dup', 'rare', null, null);
    }

    /**
     * 🎯 update fields and guard key conflicts.
     */
    public function test_update_changes_fields_and_blocks_conflict(): void
    {
        ## --------| Arrange |-------- ##
        $relic = (new Relic())
            ->setKey('old')
            ->setLabel('Ancienne')
            ->setRarity('rare');
        $this->setId($relic, 4);

        $this->repository->method('getById')->with(4)->willReturn($relic);
        $this->repository->method('getByKey')->willReturnCallback(
            static fn(string $key) => $key === 'taken' ? new Relic() : null
        );

        ## --------| Act |-------- ##
        $updated = $this->service->update(4, 'new', 'Nouvelle', 'legendary', 'Desc', 'pic.png');

        ## --------| Assert |-------- ##
        $this->assertSame('new', $updated->getKey());
        $this->assertSame('Nouvelle', $updated->getLabel());
        $this->assertSame('legendary', $updated->getRarity());
        $this->assertSame('Desc', $updated->getDescription());
        $this->assertSame('pic.png', $updated->getPicture());

        ## --------| Act |-------- ##
        $this->expectException(RelicKeyAlreadyExistsException::class);
        $this->service->update(4, 'taken');
    }

    /**
     * 🎯 throw when relic id missing.
     */
    public function test_get_or_fail_throws_when_missing(): void
    {
        ## --------| Arrange |-------- ##
        $this->repository->method('getById')->with(404)->willReturn(null);

        ## --------| Act |-------- ##
        $this->expectException(RelicNotFoundException::class);
        $this->service->getOrFail(404);
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
