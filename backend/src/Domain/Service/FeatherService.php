<?php

namespace App\Domain\Service;

use App\Domain\Entity\Feather;
use App\Domain\Exception\CannotDelete\CannotDeleteFeatherInUseException;
use App\Domain\Exception\Conflict\FeatherKeyAlreadyExistsException;
use App\Domain\Exception\NotFound\FeatherNotFoundException;
use App\Domain\Repository\FeatherRepositoryInterface;

final class FeatherService
{
    public function __construct(
        private readonly FeatherRepositoryInterface $featherRepository,
    ) {
    }

    /**
     * @return Feather[]
     */
    public function listAll(): array
    {
        return $this->featherRepository->findAllOrdered();
    }

    /**
     * @return Feather[]
     */
    public function listPage(int $limit, int $offset, string $sort, string $direction): array
    {
        return $this->featherRepository->findPage($limit, $offset, $sort, $direction);
    }

    public function countFeathers(): int
    {
        return $this->featherRepository->countAll();
    }

    public function getOrFail(int $id): Feather
    {
        $feather = $this->featherRepository->getById($id);

        if ($feather === null) {
            throw new FeatherNotFoundException('Feather not found for id ' . $id . '.');
        }

        return $feather;
    }

    public function create(string $key, string $label, ?string $description, ?string $icon): Feather
    {
        if ($this->featherRepository->getByKey($key) !== null) {
            throw new FeatherKeyAlreadyExistsException('Feather key already exists: ' . $key . '.');
        }

        $feather = new Feather();
        $feather->setKey($key);
        $feather->setLabel($label);
        $feather->setDescription($description);
        $feather->setIcon($icon);

        $this->featherRepository->save($feather);

        return $feather;
    }

    public function update(
        int $id,
        ?string $key = null,
        ?string $label = null,
        ?string $description = null,
        ?string $icon = null
    ): Feather {
        $feather = $this->getOrFail($id);

        if ($key !== null && $key !== $feather->getKey()) {
            $existing = $this->featherRepository->getByKey($key);
            if ($existing !== null) {
                throw new FeatherKeyAlreadyExistsException('Feather key already exists: ' . $key . '.');
            }
            $feather->setKey($key);
        }

        if ($label !== null) {
            $feather->setLabel($label);
        }

        if ($description !== null) {
            $feather->setDescription($description);
        }

        if ($icon !== null) {
            $feather->setIcon($icon);
        }

        $this->featherRepository->save($feather);

        return $feather;
    }

    public function delete(int $id): void
    {
        $feather = $this->getOrFail($id);

        $this->featherRepository->delete($feather);
    }
}
