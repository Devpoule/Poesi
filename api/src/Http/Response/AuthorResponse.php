<?php

declare(strict_types=1);

namespace App\Http\Response;

use App\Domain\Entity\Author;
use App\Domain\Entity\AuthorRelic;
use App\Domain\Lore\LoreCatalog;

final class AuthorResponse
{
    public function __construct(
        private readonly LoreCatalog $loreCatalog,
    ) {
    }

    /**
     * @return array{
     *   id:int|null,
     *   pseudo:string|null,
     *   relics:list<array{
     *     key:string,
     *     label:string,
     *     description:string,
     *     picture:string,
     *     rarity:string,
     *     grantedAt:string,
     *     reason:string|null
     *   }>
     * }
     */
    public function item(Author $author): array
    {
        return [
            'id'     => $author->getId(),
            'pseudo' => $author->getPseudo(),
            'relics' => $this->mapRelics($author),
        ];
    }

    /**
     * @return list<array{
     *   key:string,
     *   label:string,
     *   description:string,
     *   picture:string,
     *   rarity:string,
     *   grantedAt:string,
     *   reason:string|null
     * }>
     */
    private function mapRelics(Author $author): array
    {
        $result = [];

        foreach ($author->getRelics() as $authorRelic) {
            if (!$authorRelic instanceof AuthorRelic) {
                continue;
            }

            $key = $authorRelic->getRelicKey();
            $lore = $this->loreCatalog->getRelic($key);

            $result[] = [
                'key'       => $key,
                'label'     => (string) ($lore['label'] ?? $key),
                'description'=> (string) ($lore['description'] ?? ''),
                'picture'   => (string) ($lore['picture'] ?? ''),
                'rarity'    => (string) ($lore['rarity'] ?? 'unknown'),
                'grantedAt' => $authorRelic->getGrantedAt()->format(\DateTimeInterface::ATOM),
                'reason'    => $authorRelic->getReason(),
            ];
        }

        return $result;
    }

    /**
     * @param iterable<Author> $authors
     *
     * @return list<array{
     *   id:int|null,
     *   pseudo:string|null,
     *   relics:list<array{
     *     key:string,
     *     label:string,
     *     description:string,
     *     picture:string,
     *     rarity:string,
     *     grantedAt:string,
     *     reason:string|null
     *   }>
     * }>
     */
    public function collection(iterable $authors): array
    {
        $result = [];

        foreach ($authors as $author) {
            if (!$author instanceof Author) {
                continue;
            }

            $result[] = $this->item($author);
        }

        return $result;
    }
}
