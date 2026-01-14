<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Relic;

/**
 * @extends EntityRepositoryInterface<Relic>
 */
interface RelicRepositoryInterface extends EntityRepositoryInterface
{
    /**
     * @return Relic[]
     */
    public function findAllOrdered(): array;

    /**
     * @return Relic[]
     */
    public function findPage(int $limit, int $offset, string $sort, string $direction): array;

    public function countAll(): int;

    public function getByKey(string $key): ?Relic;

    public function save(object $relic): void;

    public function delete(object $relic): void;
}
