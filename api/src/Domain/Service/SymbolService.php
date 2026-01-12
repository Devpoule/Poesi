<?php

namespace App\Domain\Service;

use App\Domain\Entity\Symbol;
use App\Domain\Exception\CannotDelete\CannotDeleteSymbolInUseException;
use App\Domain\Exception\Conflict\SymbolKeyAlreadyExistsException;
use App\Domain\Exception\NotFound\SymbolNotFoundException;
use App\Domain\Repository\SymbolRepositoryInterface;

final class SymbolService
{
    public function __construct(
        private readonly SymbolRepositoryInterface $symbolRepository,
    ) {
    }

    /**
     * @return Symbol[]
     */
    public function listAll(): array
    {
        return $this->symbolRepository->findAllOrdered();
    }

    /**
     * @return Symbol[]
     */
    public function listPage(int $limit, int $offset, string $sort, string $direction): array
    {
        return $this->symbolRepository->findPage($limit, $offset, $sort, $direction);
    }

    public function countSymbols(): int
    {
        return $this->symbolRepository->countAll();
    }

    public function getOrFail(int $id): Symbol
    {
        $symbol = $this->symbolRepository->getById($id);

        if ($symbol === null) {
            throw new SymbolNotFoundException('Symbol not found for id ' . $id . '.');
        }

        return $symbol;
    }

    public function create(string $key, string $label, ?string $description, ?string $picture): Symbol
    {
        if ($this->symbolRepository->getByKey($key) !== null) {
            throw new SymbolKeyAlreadyExistsException('Symbol key already exists: ' . $key . '.');
        }

        $symbol = new Symbol();
        $symbol->setKey($key);
        $symbol->setLabel($label);
        $symbol->setDescription($description);
        $symbol->setPicture($picture);

        $this->symbolRepository->save($symbol);

        return $symbol;
    }

    public function update(
        int $id,
        ?string $key = null,
        ?string $label = null,
        ?string $description = null,
        ?string $picture = null
    ): Symbol {
        $symbol = $this->getOrFail($id);

        if ($key !== null && $key !== $symbol->getKey()) {
            if ($this->symbolRepository->getByKey($key) !== null) {
                throw new SymbolKeyAlreadyExistsException('Symbol key already exists: ' . $key . '.');
            }
            $symbol->setKey($key);
        }

        if ($label !== null) {
            $symbol->setLabel($label);
        }

        if ($description !== null) {
            $symbol->setDescription($description);
        }

        if ($picture !== null) {
            $symbol->setPicture($picture);
        }

        $this->symbolRepository->save($symbol);

        return $symbol;
    }

    public function delete(int $id): void
    {
        $symbol = $this->getOrFail($id);

        $this->symbolRepository->delete($symbol);
    }
}
