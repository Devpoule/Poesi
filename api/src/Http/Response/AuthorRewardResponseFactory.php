<?php

namespace App\Http\Response;

use App\Domain\Entity\AuthorReward;

/**
 * Builds API-friendly payloads for AuthorReward entities.
 */
class AuthorRewardResponseFactory
{
    /**
     * Transform a single AuthorReward entity into an array payload.
     *
     * @return array<string,mixed>
     */
    public static function fromEntity(AuthorReward $authorReward): array
    {
        return [
            'id'        => $authorReward->getId(),
            'earnedAt'  => $authorReward->getEarnedAt()->format(DATE_ATOM),

            'author' => $authorReward->getAuthor() ? [
                'id'     => $authorReward->getAuthor()->getId(),
                'pseudo' => $authorReward->getAuthor()->getPseudo(),
            ] : null,

            'reward' => $authorReward->getReward() ? [
                'id'    => $authorReward->getReward()->getId(),
                'code'  => $authorReward->getReward()->getCode(),
                'label' => $authorReward->getReward()->getLabel(),
            ] : null,
        ];
    }

    /**
     * Transform a list of AuthorReward entities into an array payload.
     *
     * @param iterable<AuthorReward> $items
     *
     * @return array<int,array<string,mixed>>
     */
    public static function fromCollection(iterable $items): array
    {
        $mapped = [];

        foreach ($items as $item) {
            $mapped[] = self::fromEntity($item);
        }

        return $mapped;
    }
}
