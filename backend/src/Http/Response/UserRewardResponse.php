<?php

namespace App\Http\Response;

use App\Domain\Entity\UserReward;

final class UserRewardResponse
{
    public function __construct(
        private readonly UserResponse $userResponse,
        private readonly RewardResponse $rewardResponse,
    ) {
    }

    /**
     * @return array{
     *   id:int|null,
     *   createdAt:string,
     *   user:array<string,mixed>,
     *   reward:array<string,mixed>
     * }
     */
    public function item(UserReward $userReward): array
    {
        return [
            'id'        => $userReward->getId(),
            'createdAt' => $userReward->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'user'      => $this->userResponse->item($userReward->getUser()),
            'reward'    => $this->rewardResponse->item($userReward->getReward()),
        ];
    }

    /**
     * @param iterable<UserReward> $userRewards
     *
     * @return array<int, array<string, mixed>>
     */
    public function collection(iterable $userRewards): array
    {
        $result = [];
        foreach ($userRewards as $userReward) {
            $result[] = $this->item($userReward);
        }
        return $result;
    }
}
