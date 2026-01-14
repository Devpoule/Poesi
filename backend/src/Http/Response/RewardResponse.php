<?php

namespace App\Http\Response;

use App\Domain\Entity\Reward;

final class RewardResponse
{
    /**
     * @return array{id:int|null, code:string, label:string}
     */
    public function item(Reward $reward): array
    {
        return [
            'id'    => $reward->getId(),
            'code'  => $reward->getCode(),
            'label' => $reward->getLabel(),
        ];
    }

    /**
     * @param iterable<Reward> $rewards
     *
     * @return array<int, array<string, mixed>>
     */
    public function collection(iterable $rewards): array
    {
        $result = [];
        foreach ($rewards as $reward) {
            $result[] = $this->item($reward);
        }
        return $result;
    }
}
