<?php

namespace App\Http\Response;

use App\Domain\Entity\AuthorReward;

final class AuthorRewardResponse
{
    public function __construct(
        private readonly AuthorResponse $authorResponse,
        private readonly RewardResponse $rewardResponse,
    ) {
    }

    /**
     * @return array{
     *   id:int|null,
     *   createdAt:string,
     *   author:array<string,mixed>,
     *   reward:array<string,mixed>
     * }
     */
    public function item(AuthorReward $authorReward): array
    {
        return [
            'id'        => $authorReward->getId(),
            'createdAt' => $authorReward->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'author'    => $this->authorResponse->item($authorReward->getAuthor()),
            'reward'    => $this->rewardResponse->item($authorReward->getReward()),
        ];
    }

    /**
     * @param iterable<AuthorReward> $authorRewards
     *
     * @return array<int, array<string, mixed>>
     */
    public function collection(iterable $authorRewards): array
    {
        $result = [];
        foreach ($authorRewards as $authorReward) {
            $result[] = $this->item($authorReward);
        }
        return $result;
    }
}
