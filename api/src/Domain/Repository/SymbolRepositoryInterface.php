<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Symbol;

/**
 * @extends EntityRepositoryInterface<Symbol>
 */
interface SymbolRepositoryInterface extends EntityRepositoryInterface
{
    /**
     * @return Symbol[]
     */
    public function findAllOrdered(): array;

    /**
     * @return Symbol[]
     */
    public function findPage(int $limit, int $offset, string $sort, string $direction): array;

    public function countAll(): int;

    public function getByKey(string $key): ?Symbol;

    public function save(object $symbol): void;

    public function delete(object $symbol): void;
}
