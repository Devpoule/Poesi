<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service;

use App\Domain\Entity\Mood;
use App\Domain\Exception\Conflict\MoodKeyAlreadyExistsException;
use App\Domain\Exception\NotFound\MoodNotFoundException;
use App\Domain\Repository\MoodRepositoryInterface;
use App\Domain\Service\MoodService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MoodServiceTest extends TestCase
{
    private MoodRepositoryInterface&MockObject $repository;
    private MoodService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(MoodRepositoryInterface::class);
        $this->service = new MoodService($this->repository);
    }

    /**
     * 🎯 create mood with unique key and persist.
     */
    public function test_create_persists_mood(): void
    {
        ## --------| Arrange |-------- ##
        $this->repository->method('getByKey')->with('calm')->willReturn(null);

        $captured = null;
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(static function (Mood $mood) use (&$captured): void {
                $captured = $mood;
            });

        ## --------| Act |-------- ##
        $mood = $this->service->create('calm', 'Calme', 'Respirant', 'icon.svg');

        ## --------| Assert |-------- ##
        $this->assertSame($captured, $mood);
        $this->assertSame('calm', $mood->getKey());
        $this->assertSame('Calme', $mood->getLabel());
        $this->assertSame('Respirant', $mood->getDescription());
        $this->assertSame('icon.svg', $mood->getIcon());
    }

    /**
     * 🎯 forbid creating a duplicate key.
     */
    public function test_create_throws_when_key_exists(): void
    {
        ## --------| Arrange |-------- ##
        $this->repository->method('getByKey')->with('fire')->willReturn(new Mood());

        ## --------| Act |-------- ##
        $this->expectException(MoodKeyAlreadyExistsException::class);
        $this->service->create('fire', 'Feu', null, null);
    }

    /**
     * 🎯 update mood key when available; refuse when conflicting.
     */
    public function test_update_allows_unique_key_and_blocks_duplicates(): void
    {
        ## --------| Arrange |-------- ##
        $mood = (new Mood())
            ->setKey('old')
            ->setLabel('Old')
            ->setDescription('Desc');
        $this->setId($mood, 1);

        $this->repository->method('getById')->with(1)->willReturn($mood);
        $this->repository->method('getByKey')->willReturnCallback(
            static fn(string $key) => $key === 'taken' ? new Mood() : null
        );

        ## --------| Act |-------- ##
        $updated = $this->service->update(1, 'fresh', 'New', 'New desc', 'icon.png');

        ## --------| Assert |-------- ##
        $this->assertSame('fresh', $updated->getKey());
        $this->assertSame('New', $updated->getLabel());
        $this->assertSame('New desc', $updated->getDescription());
        $this->assertSame('icon.png', $updated->getIcon());

        ## --------| Act |-------- ##
        $this->expectException(MoodKeyAlreadyExistsException::class);
        $this->service->update(1, 'taken');
    }

    /**
     * 🎯 throw when mood id is unknown.
     */
    public function test_get_or_fail_throws_when_missing(): void
    {
        ## --------| Arrange |-------- ##
        $this->repository->method('getById')->with(404)->willReturn(null);

        ## --------| Act |-------- ##
        $this->expectException(MoodNotFoundException::class);
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
