<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Mood;

/**
 * @extends EntityRepositoryInterface<Mood>
 */
interface MoodRepositoryInterface extends EntityRepositoryInterface
{
    /**
     * @return Mood[]
     */
    public function findAllOrdered(): array;

    /**
     * @return Mood[]
     */
    public function findPage(int $limit, int $offset, string $sort, string $direction): array;

    public function countAll(): int;

    public function getByKey(string $key): ?Mood;

    public function save(object $mood): void;

    public function delete(object $mood): void;
}
