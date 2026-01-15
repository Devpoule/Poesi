<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service;

use App\Domain\Entity\Totem;
use App\Domain\Exception\NotFound\TotemNotFoundException;
use App\Domain\Repository\TotemRepositoryInterface;
use App\Domain\Service\TotemService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TotemServiceTest extends TestCase
{
    private TotemRepositoryInterface&MockObject $repository;
    private TotemService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TotemRepositoryInterface::class);
        $this->service = new TotemService($this->repository);
    }

    /**
     * 🎯 create and persist a new totem.
     */
    public function test_create_totem_persists_entity(): void
    {
        ## --------| Arrange |-------- ##
        $captured = null;
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(static function (Totem $totem) use (&$captured): void {
                $captured = $totem;
            });

        ## --------| Act |-------- ##
        $totem = $this->service->createTotem('Heron', 'Desc', 'pic.png');

        ## --------| Assert |-------- ##
        $this->assertSame($captured, $totem);
        $this->assertSame('Heron', $totem->getName());
        $this->assertSame('Desc', $totem->getDescription());
        $this->assertSame('pic.png', $totem->getPicture());
    }

    /**
     * 🎯 update totem fields.
     */
    public function test_update_totem_changes_values(): void
    {
        ## --------| Arrange |-------- ##
        $totem = (new Totem())
            ->setName('Old')
            ->setDescription('Old desc')
            ->setPicture('old.png');
        $this->setId($totem, 12);

        $this->repository->method('getById')->with(12)->willReturn($totem);
        $this->repository->expects($this->once())->method('save')->with($totem);

        ## --------| Act |-------- ##
        $updated = $this->service->updateTotem(12, 'New', 'New desc', 'new.png');

        ## --------| Assert |-------- ##
        $this->assertSame('New', $updated->getName());
        $this->assertSame('New desc', $updated->getDescription());
        $this->assertSame('new.png', $updated->getPicture());
    }

    /**
     * 🎯 throw when totem is missing.
     */
    public function test_get_totem_or_fail_throws_when_missing(): void
    {
        ## --------| Arrange |-------- ##
        $this->repository->method('getById')->with(404)->willReturn(null);

        ## --------| Act |-------- ##
        $this->expectException(TotemNotFoundException::class);
        $this->service->getTotemOrFail(404);
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
