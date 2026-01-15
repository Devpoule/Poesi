<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service;

use App\Domain\Entity\Symbol;
use App\Domain\Exception\Conflict\SymbolKeyAlreadyExistsException;
use App\Domain\Exception\NotFound\SymbolNotFoundException;
use App\Domain\Repository\SymbolRepositoryInterface;
use App\Domain\Service\SymbolService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SymbolServiceTest extends TestCase
{
    private SymbolRepositoryInterface&MockObject $repository;
    private SymbolService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(SymbolRepositoryInterface::class);
        $this->service = new SymbolService($this->repository);
    }

    /**
     * 🎯 create symbol when key is unique.
     */
    public function test_create_persists_symbol(): void
    {
        ## --------| Arrange |-------- ##
        $this->repository->method('getByKey')->with('halo')->willReturn(null);

        $captured = null;
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(static function (Symbol $symbol) use (&$captured): void {
                $captured = $symbol;
            });

        ## --------| Act |-------- ##
        $symbol = $this->service->create('halo', 'Halo', 'Clarte', 'halo.png');

        ## --------| Assert |-------- ##
        $this->assertSame($captured, $symbol);
        $this->assertSame('halo', $symbol->getKey());
        $this->assertSame('Halo', $symbol->getLabel());
        $this->assertSame('Clarte', $symbol->getDescription());
        $this->assertSame('halo.png', $symbol->getPicture());
    }

    /**
     * 🎯 prevent duplicate key creation.
     */
    public function test_create_throws_on_existing_key(): void
    {
        ## --------| Arrange |-------- ##
        $this->repository->method('getByKey')->with('wings')->willReturn(new Symbol());

        ## --------| Act |-------- ##
        $this->expectException(SymbolKeyAlreadyExistsException::class);
        $this->service->create('wings', 'Ailes', null, null);
    }

    /**
     * 🎯 update symbol fields and check duplicate key protection.
     */
    public function test_update_changes_fields_and_blocks_conflict(): void
    {
        ## --------| Arrange |-------- ##
        $symbol = (new Symbol())
            ->setKey('old')
            ->setLabel('Ancien');
        $this->setId($symbol, 2);

        $this->repository->method('getById')->with(2)->willReturn($symbol);
        $this->repository->method('getByKey')->willReturnCallback(
            static fn(string $key) => $key === 'taken' ? new Symbol() : null
        );

        ## --------| Act |-------- ##
        $updated = $this->service->update(2, 'fresh', 'Nouveau', 'Desc', 'pic.png');

        ## --------| Assert |-------- ##
        $this->assertSame('fresh', $updated->getKey());
        $this->assertSame('Nouveau', $updated->getLabel());
        $this->assertSame('Desc', $updated->getDescription());
        $this->assertSame('pic.png', $updated->getPicture());

        ## --------| Act |-------- ##
        $this->expectException(SymbolKeyAlreadyExistsException::class);
        $this->service->update(2, 'taken');
    }

    /**
     * 🎯 throw when symbol id is unknown.
     */
    public function test_get_or_fail_throws_when_missing(): void
    {
        ## --------| Arrange |-------- ##
        $this->repository->method('getById')->with(404)->willReturn(null);

        ## --------| Act |-------- ##
        $this->expectException(SymbolNotFoundException::class);
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
