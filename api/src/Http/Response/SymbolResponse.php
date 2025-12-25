<?php

namespace App\Http\Response;

use App\Domain\Entity\Symbol;

final class SymbolResponse
{
    /**
     * @return array{
     *   id:int|null,
     *   key:string,
     *   label:string,
     *   description:string|null,
     *   picture:string|null
     * }
     */
    public function item(Symbol $symbol): array
    {
        return [
            'id'          => $symbol->getId(),
            'key'         => $symbol->getKey(),
            'label'       => $symbol->getLabel(),
            'description' => $symbol->getDescription(),
            'picture'     => $symbol->getPicture(),
        ];
    }

    /**
     * @param iterable<Symbol> $symbols
     * @return array<int, array<string, mixed>>
     */
    public function collection(iterable $symbols): array
    {
        $result = [];
        foreach ($symbols as $symbol) {
            $result[] = $this->item($symbol);
        }
        return $result;
    }
}
