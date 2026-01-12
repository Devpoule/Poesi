<?php

namespace App\Domain\Service;

use App\Domain\Entity\Relic;
use App\Domain\Exception\CannotDelete\CannotDeleteRelicInUseException;
use App\Domain\Exception\Conflict\RelicKeyAlreadyExistsException;
use App\Domain\Exception\NotFound\RelicNotFoundException;
use App\Domain\Repository\RelicRepositoryInterface;

final class RelicService
{
    public function __construct(
        private readonly RelicRepositoryInterface $relicRepository,
    ) {
    }

    /**
     * @return Relic[]
     */
    public function listAll(): array
    {
        return $this->relicRepository->findAllOrdered();
    }

    /**
     * @return Relic[]
     */
    public function listPage(int $limit, int $offset, string $sort, string $direction): array
    {
        return $this->relicRepository->findPage($limit, $offset, $sort, $direction);
    }

    public function countRelics(): int
    {
        return $this->relicRepository->countAll();
    }

    public function getOrFail(int $id): Relic
    {
        $relic = $this->relicRepository->getById($id);

        if ($relic === null) {
            throw new RelicNotFoundException('Relic not found for id ' . $id . '.');
        }

        return $relic;
    }

    public function create(
        string $key,
        string $label,
        string $rarity,
        ?string $description,
        ?string $picture
    ): Relic {
        if ($this->relicRepository->getByKey($key) !== null) {
            throw new RelicKeyAlreadyExistsException('Relic key already exists: ' . $key . '.');
        }

        $relic = new Relic();
        $relic->setKey($key);
        $relic->setLabel($label);
        $relic->setRarity($rarity);
        $relic->setDescription($description);
        $relic->setPicture($picture);

        $this->relicRepository->save($relic);

        return $relic;
    }

    public function update(
        int $id,
        ?string $key = null,
        ?string $label = null,
        ?string $rarity = null,
        ?string $description = null,
        ?string $picture = null
    ): Relic {
        $relic = $this->getOrFail($id);

        if ($key !== null && $key !== $relic->getKey()) {
            if ($this->relicRepository->getByKey($key) !== null) {
                throw new RelicKeyAlreadyExistsException('Relic key already exists: ' . $key . '.');
            }
            $relic->setKey($key);
        }

        if ($label !== null) {
            $relic->setLabel($label);
        }

        if ($rarity !== null) {
            $relic->setRarity($rarity);
        }

        if ($description !== null) {
            $relic->setDescription($description);
        }

        if ($picture !== null) {
            $relic->setPicture($picture);
        }

        $this->relicRepository->save($relic);

        return $relic;
    }

    public function delete(int $id): void
    {
        $relic = $this->getOrFail($id);

        $this->relicRepository->delete($relic);
    }
}
