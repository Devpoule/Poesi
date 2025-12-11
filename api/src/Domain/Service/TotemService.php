<?php

namespace App\Domain\Service;

use App\Domain\Entity\Totem;
use App\Domain\Exception\TotemNotFoundException;
use App\Domain\Repository\TotemRepositoryInterface;

/**
 * Domain service responsible for managing Totem catalog.
 */
class TotemService
{
    public function __construct(
        private TotemRepositoryInterface $totemRepository
    ) {
    }

    /**
     * Create a new Totem.
     *
     * @param string      $name
     * @param string|null $description
     * @param string|null $picture
     *
     * @return Totem
     */
    public function createTotem(
        string $name,
        ?string $description = null,
        ?string $picture = null
    ): Totem {
        $totem = new Totem();
        $totem->setName($name);
        $totem->setDescription($description);
        $totem->setPicture($picture);

        $this->totemRepository->save($totem);

        return $totem;
    }

    /**
     * Retrieve a totem by id or throw an exception if not found.
     *
     * @param int $totemId
     *
     * @return Totem
     */
    public function getTotemOrFail(int $totemId): Totem
    {
        $totem = $this->totemRepository->getById($totemId);

        if ($totem === null) {
            throw new TotemNotFoundException('Totem not found for id ' . $totemId . '.');
        }

        return $totem;
    }

    /**
     * List all totems ordered for display.
     *
     * @return Totem[]
     */
    public function listAll(): array
    {
        return $this->totemRepository->findAllOrdered();
    }

    /**
     * Update a Totem.
     * Only non-null parameters are applied.
     *
     * @param int         $totemId
     * @param string|null $name
     * @param string|null $description
     * @param string|null $picture
     *
     * @return Totem
     */
    public function updateTotem(
        int $totemId,
        ?string $name = null,
        ?string $description = null,
        ?string $picture = null
    ): Totem {
        $totem = $this->getTotemOrFail($totemId);

        if ($name !== null) {
            $totem->setName($name);
        }

        if ($description !== null) {
            $totem->setDescription($description);
        }

        if ($picture !== null) {
            $totem->setPicture($picture);
        }

        $this->totemRepository->save($totem);

        return $totem;
    }

    /**
     * Delete a totem by id.
     *
     * @param int $totemId
     *
     * @return void
     */
    public function deleteTotem(int $totemId): void
    {
        $totem = $this->getTotemOrFail($totemId);

        $this->totemRepository->delete($totem);
    }
}
