<?php

declare(strict_types=1);

namespace App\Services\Naming;

use App\Contracts\AstrologyEngineInterface;
use Carbon\Carbon;

class MissingElementCalculator
{
    /** @var list<string> */
    private const ELEMENTS = ['kim', 'moc', 'thuy', 'hoa', 'tho'];

    /** @var array<string, string> */
    private const GENERATES = [
        'kim' => 'thuy',
        'thuy' => 'moc',
        'moc' => 'hoa',
        'hoa' => 'tho',
        'tho' => 'kim',
    ];

    public function __construct(
        private readonly AstrologyEngineInterface $astrology,
    ) {}

    /**
     * @param list<string> $allowedElements
     * @return array{
     *   bazi: array<string, mixed>,
     *   target_elements: list<string>,
     *   fallback_used: bool
     * }
     */
    public function analyze(Carbon $babyBirthDate, array $allowedElements): array
    {
        $bazi = $this->astrology->calculateBaziProfile($babyBirthDate);
        $missing = array_values($bazi['missing_elements'] ?? []);
        $fallback = false;
        $target = [];

        if ($missing) {
            $target = array_values(array_intersect($missing, $allowedElements));
            if (!$target) {
                $target = array_slice($missing, 0, 2);
            }
        } else {
            $fallback = true;
            $counts = $bazi['element_count'] ?? [];
            $pool = $allowedElements ?: self::ELEMENTS;
            usort($pool, static fn (string $a, string $b): int => ($counts[$a] ?? 0) <=> ($counts[$b] ?? 0));
            $target = array_slice($pool, 0, 2);

            if (!$target) {
                $dominant = (string) ($bazi['dominant_element'] ?? 'moc');
                $target = [self::GENERATES[$dominant] ?? 'hoa'];
            }
        }

        return [
            'bazi' => $bazi,
            'target_elements' => array_values(array_unique($target)),
            'fallback_used' => $fallback,
        ];
    }
}

