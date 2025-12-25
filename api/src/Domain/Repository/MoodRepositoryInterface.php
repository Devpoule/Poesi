<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Mood;

/**
 * @extends EntityRepositoryInterface<Mood>
 */
interface MoodRepositoryInterface extends EntityRepositoryInterface
{
    /**
     * @return Mood[]
     */
    public function findAllOrdered(): array;

    public function getByKey(string $key): ?Mood;

    public function save(object $mood): void;

    public function delete(object $mood): void;
}
