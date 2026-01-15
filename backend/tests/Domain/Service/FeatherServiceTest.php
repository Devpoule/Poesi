<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service;

use App\Domain\Entity\Feather;
use App\Domain\Exception\Conflict\FeatherKeyAlreadyExistsException;
use App\Domain\Exception\NotFound\FeatherNotFoundException;
use App\Domain\Repository\FeatherRepositoryInterface;
use App\Domain\Service\FeatherService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FeatherServiceTest extends TestCase
{
    private FeatherRepositoryInterface&MockObject $repository;
    private FeatherService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(FeatherRepositoryInterface::class);
        $this->service = new FeatherService($this->repository);
    }

    /**
     * 🎯 create a feather with unique key.
     */
    public function test_create_persists_feather(): void
    {
        ## --------| Arrange |-------- ##
        $this->repository->method('getByKey')->with('soft')->willReturn(null);

        $captured = null;
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(static function (Feather $feather) use (&$captured): void {
                $captured = $feather;
            });

        ## --------| Act |-------- ##
        $feather = $this->service->create('soft', 'Douce', 'Calme', 'icon.png');

        ## --------| Assert |-------- ##
        $this->assertSame($captured, $feather);
        $this->assertSame('soft', $feather->getKey());
        $this->assertSame('Douce', $feather->getLabel());
        $this->assertSame('Calme', $feather->getDescription());
        $this->assertSame('icon.png', $feather->getIcon());
    }

    /**
     * 🎯 prevent duplicate key on creation.
     */
    public function test_create_throws_when_key_exists(): void
    {
        ## --------| Arrange |-------- ##
        $this->repository->method('getByKey')->with('hard')->willReturn(new Feather());

        ## --------| Act |-------- ##
        $this->expectException(FeatherKeyAlreadyExistsException::class);
        $this->service->create('hard', 'Dure', null, null);
    }

    /**
     * 🎯 update fields and protect against duplicate key.
     */
    public function test_update_changes_fields_and_blocks_conflict(): void
    {
        ## --------| Arrange |-------- ##
        $feather = (new Feather())
            ->setKey('old')
            ->setLabel('Ancienne');
        $this->setId($feather, 3);

        $this->repository->method('getById')->with(3)->willReturn($feather);
        $this->repository->method('getByKey')->willReturnCallback(
            static fn(string $key) => $key === 'taken' ? new Feather() : null
        );

        ## --------| Act |-------- ##
        $updated = $this->service->update(3, 'new', 'Nouvelle', 'Desc', 'ic.png');

        ## --------| Assert |-------- ##
        $this->assertSame('new', $updated->getKey());
        $this->assertSame('Nouvelle', $updated->getLabel());
        $this->assertSame('Desc', $updated->getDescription());
        $this->assertSame('ic.png', $updated->getIcon());

        ## --------| Act |-------- ##
        $this->expectException(FeatherKeyAlreadyExistsException::class);
        $this->service->update(3, 'taken');
    }

    /**
     * 🎯 throw when id not found.
     */
    public function test_get_or_fail_throws_when_missing(): void
    {
        ## --------| Arrange |-------- ##
        $this->repository->method('getById')->with(404)->willReturn(null);

        ## --------| Act |-------- ##
        $this->expectException(FeatherNotFoundException::class);
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
