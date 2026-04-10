<?php

declare(strict_types=1);

namespace App\DTO\Astrology;

class ChartDTO
{
    /**
     * @param array{
     *   birthDate: string,
     *   gender: string,
     *   canChiYear: string,
     *   canChiDay: string,
     *   lunarDateLabel: string,
     *   menh: string,
     *   cuc: string
     * } $profile
     * @param list<PalaceDTO> $palaces
     */
    public function __construct(
        public readonly array $profile,
        public readonly array $palaces,
    ) {}

    /**
     * @return array{
     *   profile: array{
     *     birthDate: string,
     *     gender: string,
     *     canChiYear: string,
     *     canChiDay: string,
     *     lunarDateLabel: string,
     *     menh: string,
     *     cuc: string
     *   },
     *   palaces: list<array{
     *     name: string,
     *     index: int,
     *     score: int,
     *     summary: string,
     *     stars: list<array{name: string, quality: string, element: string}>
     *   }>
     * }
     */
    public function toArray(): array
    {
        return [
            'profile' => $this->profile,
            'palaces' => array_map(
                static fn (PalaceDTO $palace): array => $palace->toArray(),
                $this->palaces,
            ),
        ];
    }
}

