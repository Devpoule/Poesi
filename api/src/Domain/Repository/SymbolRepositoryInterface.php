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

    public function getByKey(string $key): ?Symbol;

    public function save(object $symbol): void;

    public function delete(object $symbol): void;
}
