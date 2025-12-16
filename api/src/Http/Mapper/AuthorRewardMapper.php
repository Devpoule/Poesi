<?php

namespace App\Http\Mapper;

use App\Domain\Entity\AuthorReward;

/**
 * Maps AuthorReward domain entities to API-friendly arrays.
 */
final class AuthorRewardMapper
{
    public function __construct(
        private readonly RewardMapper $rewardMapper,
        private readonly AuthorMapper $authorMapper,
    ) {
    }

    /**
     * Convert an AuthorReward entity into an array representation for the API.
     *
     * @param AuthorReward $authorReward
     *
     * @return array{
     *   id: int|null,
     *   createdAt: string,
     *   author: array<string, mixed>|null,
     *   reward: array<string, mixed>|null
     * }
     */
    public function toArray(AuthorReward $authorReward): array
    {
        return [
            'id' => $authorReward->getId(),
            'createdAt' => $authorReward->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'author' => $authorReward->getAuthor() !== null ? $this->authorMapper->toArray($authorReward->getAuthor()) : null,
            'reward' => $authorReward->getReward() !== null ? $this->rewardMapper->toArray($authorReward->getReward()) : null,
        ];
    }

    /**
     * Convert a list of AuthorReward entities into a list of arrays.
     *
     * @param AuthorReward[] $authorRewards
     *
     * @return array<int, array<string, mixed>>
     */
    public function toCollection(array $authorRewards): array
    {
        $result = [];

        foreach ($authorRewards as $authorReward) {
            if (!$authorReward instanceof AuthorReward) {
                continue;
            }

            $result[] = $this->toArray($authorReward);
        }

        return $result;
    }
}
