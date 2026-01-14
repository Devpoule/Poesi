<?php

namespace App\Domain\Lore;

use App\Domain\Enum\FeatherType;
use App\Domain\Enum\MoodColor;
use App\Domain\Enum\SymbolType;

/**
 * LoreCatalog is the official dictionary that transforms a business value (enum) into UI-readable data.
 *
 * Reads immutable JSON archives and exposes narrative data aligned with domain enums.
 *
 * This class contains NO business logic.
 */
final class LoreCatalog
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $moods = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    private array $feathers = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    private array $symbols = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    private array $relics = [];

    public function __construct(string $projectDir)
    {
        $basePath = rtrim($projectDir, DIRECTORY_SEPARATOR) . '/resources/lore';

        $this->moods = $this->indexByKey(
            $this->readJson($basePath . '/moods.initial.json'),
            'key'
        );

        $this->feathers = $this->indexByKey(
            $this->readJson($basePath . '/feathers.initial.json'),
            'key'
        );

        $this->symbols = $this->indexByKey(
            $this->readJson($basePath . '/symbols.initial.json'),
            'key'
        );

        $this->relics = $this->indexByKey(
            $this->readJson($basePath . '/relics.initial.json'),
            'key'
        );
    }

    #################
    ###   Moods   ###
    #################

    /**
     * Get lore data for a given mood color.
     *
     * @return array{label: string, description: string, icon: string}
     */
    public function getMood(MoodColor $color): array
    {
        return $this->moods[$color->value]
            ?? throw new \LogicException(sprintf('Unknown mood color: %s', $color->value));
    }

    /**
     * Get all moods lore.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAllMoods(): array
    {
        return $this->moods;
    }

    #################
    ###  Feathers ###
    #################

    /**
     * Get lore data for a given feather type.
     *
     * @return array{label: string, description: string, icon: string}
     */
    public function getFeather(FeatherType $type): array
    {
        return $this->feathers[$type->value]
            ?? throw new \LogicException(sprintf('Unknown feather type: %s', $type->value));
    }

    /**
     * Get all feathers lore.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAllFeathers(): array
    {
        return $this->feathers;
    }

    #################
    ###  Symbols  ###
    #################

    /**
     * Get lore data for a given symbol type.
     *
     * @return array{label: string, description: string, picture: string}
     */
    public function getSymbol(SymbolType $type): array
    {
        return $this->symbols[$type->value]
            ?? throw new \LogicException(sprintf('Unknown symbol type: %s', $type->value));
    }

    /**
     * Get all symbols lore (indexed by type).
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAllSymbols(): array
    {
        return $this->symbols;
    }

    /**
     * Get symbols lore as a list (not indexed).
     *
     * @return list<array<string, mixed>>
     */
    public function getSymbols(): array
    {
        return array_values($this->symbols);
    }

    #################
    ###  Relics   ###
    #################

    /**
     * Get lore data for a given relic key.
     *
     * @return array{key:string,label:string,description:string,picture:string,rarity:string}
     */
    public function getRelic(string $key): array
    {
        return $this->relics[$key]
            ?? throw new \LogicException(sprintf('Unknown relic key: %s', $key));
    }

    /**
     * Get all relics lore (indexed by key).
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAllRelics(): array
    {
        return $this->relics;
    }

    /**
     * Get relics lore as a list (not indexed).
     *
     * @return list<array<string, mixed>>
     */
    public function getRelics(): array
    {
        return array_values($this->relics);
    }

    #################
    ### Internals ###
    #################

    /***
     *
     * @param string $path
     * @throws \RuntimeException
     * 
     * @return list<array<string, mixed>>
     */
    private function readJson(string $path): array
    {
        if (!is_file($path)) {
            throw new \RuntimeException(sprintf('Lore file not found: %s', $path));
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new \RuntimeException(sprintf('Unable to read lore file: %s', $path));
        }

        /** @var mixed $data */
        $data = json_decode($content, true, flags: JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new \RuntimeException(sprintf('Invalid JSON structure in: %s', $path));
        }

        /** @var list<array<string, mixed>> $data */
        return $data;
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @param string $key
     * @throws \RuntimeException
     * @return array
     */
    private function indexByKey(array $rows, string $key): array
    {
        $indexed = [];

        foreach ($rows as $i => $row) {
            if (!isset($row[$key]) || $row[$key] === '') {
                throw new \RuntimeException(sprintf('Missing key "%s" in lore row #%d', $key, $i));
            }

            $indexed[(string) $row[$key]] = $row;
        }

        return $indexed;
    }
}
