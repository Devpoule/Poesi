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

    public function getByKey(string $key): ?Feather;

    public function save(object $feather): void;

    public function delete(object $feather): void;
}
