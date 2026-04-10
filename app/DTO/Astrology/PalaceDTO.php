<?php

declare(strict_types=1);

namespace App\DTO\Astrology;

class PalaceDTO
{
    /**
     * @param list<StarDTO> $stars
     */
    public function __construct(
        public readonly string $name,
        public readonly int $index,
        public readonly int $score,
        public readonly string $summary,
        public readonly array $stars,
    ) {}

    /**
     * @return array{
     *   name: string,
     *   index: int,
     *   score: int,
     *   summary: string,
     *   stars: list<array{name: string, quality: string, element: string}>
     * }
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'index' => $this->index,
            'score' => $this->score,
            'summary' => $this->summary,
            'stars' => array_map(
                static fn (StarDTO $star): array => $star->toArray(),
                $this->stars,
            ),
        ];
    }
}

