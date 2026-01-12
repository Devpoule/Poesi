<?php

namespace App\Domain\Service;

use App\Domain\Entity\Mood;
use App\Domain\Exception\CannotDelete\CannotDeleteMoodInUseException;
use App\Domain\Exception\Conflict\MoodKeyAlreadyExistsException;
use App\Domain\Exception\NotFound\MoodNotFoundException;
use App\Domain\Repository\MoodRepositoryInterface;

final class MoodService
{
    public function __construct(
        private readonly MoodRepositoryInterface $moodRepository,
    ) {
    }

    /**
     * @return Mood[]
     */
    public function listAll(): array
    {
        return $this->moodRepository->findAllOrdered();
    }

    /**
     * @return Mood[]
     */
    public function listPage(int $limit, int $offset, string $sort, string $direction): array
    {
        return $this->moodRepository->findPage($limit, $offset, $sort, $direction);
    }

    public function countMoods(): int
    {
        return $this->moodRepository->countAll();
    }

    public function getOrFail(int $id): Mood
    {
        $mood = $this->moodRepository->getById($id);

        if ($mood === null) {
            throw new MoodNotFoundException('Mood not found for id ' . $id . '.');
        }

        return $mood;
    }

    public function create(string $key, string $label, ?string $description, ?string $icon): Mood
    {
        if ($this->moodRepository->getByKey($key) !== null) {
            throw new MoodKeyAlreadyExistsException('Mood key already exists: ' . $key . '.');
        }

        $mood = new Mood();
        $mood->setKey($key);
        $mood->setLabel($label);
        $mood->setDescription($description);
        $mood->setIcon($icon);

        $this->moodRepository->save($mood);

        return $mood;
    }

    public function update(
        int $id,
        ?string $key = null,
        ?string $label = null,
        ?string $description = null,
        ?string $icon = null
    ): Mood {
        $mood = $this->getOrFail($id);

        if ($key !== null && $key !== $mood->getKey()) {
            $existing = $this->moodRepository->getByKey($key);
            if ($existing !== null) {
                throw new MoodKeyAlreadyExistsException('Mood key already exists: ' . $key . '.');
            }
            $mood->setKey($key);
        }

        if ($label !== null) {
            $mood->setLabel($label);
        }

        if ($description !== null) {
            $mood->setDescription($description);
        }

        if ($icon !== null) {
            $mood->setIcon($icon);
        }

        $this->moodRepository->save($mood);

        return $mood;
    }

    public function delete(int $id): void
    {
        $mood = $this->getOrFail($id);

        $this->moodRepository->delete($mood);
    }
}
