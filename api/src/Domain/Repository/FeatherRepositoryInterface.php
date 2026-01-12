<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Feather;

/**
 * @extends EntityRepositoryInterface<Feather>
 */
interface FeatherRepositoryInterface extends EntityRepositoryInterface
{
    /**
     * @return Feather[]
     */
    public function findAllOrdered(): array;

    /**
     * @return Feather[]
     */
    public function findPage(int $limit, int $offset, string $sort, string $direction): array;

    public function countAll(): int;

    public function getByKey(string $key): ?Feather;

    public function save(object $feather): void;

    public function delete(object $feather): void;
}
