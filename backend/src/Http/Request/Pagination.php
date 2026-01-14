<?php

namespace App\Http\Request;

use Symfony\Component\HttpFoundation\Request;

final class Pagination
{
    private function __construct(
        private int $page,
        private int $limit,
        private string $sort,
        private string $direction
    ) {
    }

    /**
     * @param array<string, string> $allowedSorts
     */
    public static function fromRequest(
        Request $request,
        array $allowedSorts,
        string $defaultSort,
        string $defaultDirection = 'DESC',
        int $defaultLimit = 50,
        int $maxLimit = 200
    ): self {
        $page = (int) $request->query->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        $limit = (int) $request->query->get('limit', $defaultLimit);
        if ($limit < 1) {
            $limit = $defaultLimit;
        }
        if ($limit > $maxLimit) {
            $limit = $maxLimit;
        }

        $direction = strtoupper((string) $request->query->get('direction', $defaultDirection));
        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            $direction = $defaultDirection;
        }

        $sort = (string) $request->query->get('sort', $defaultSort);
        if (!array_key_exists($sort, $allowedSorts)) {
            $sort = $defaultSort;
        }

        return new self($page, $limit, $sort, $direction);
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }

    public function getSort(): string
    {
        return $this->sort;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }
}
