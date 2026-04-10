<?php

declare(strict_types=1);

namespace App\Services\Wedding;

use App\DTO\Wedding\PartnerDTO;
use App\DTO\Wedding\TargetYearMonthDTO;
use App\Services\Wedding\Filters\DayAuspiciousFilter;
use App\Services\Wedding\Filters\KimLauFilter;
use App\Services\Wedding\Filters\MonthBenefitFilter;
use App\Services\Wedding\Filters\WeddingFilterInterface;
use App\Services\Wedding\Filters\ZodiacClashFilter;

class WeddingDatePipeline
{
    /** @var list<WeddingFilterInterface> */
    private array $filters;

    public function __construct(
        KimLauFilter $kimLauFilter,
        MonthBenefitFilter $monthBenefitFilter,
        DayAuspiciousFilter $dayAuspiciousFilter,
        ZodiacClashFilter $zodiacClashFilter,
    ) {
        $this->filters = [
            $kimLauFilter,
            $monthBenefitFilter,
            $dayAuspiciousFilter,
            $zodiacClashFilter,
        ];
    }

    /**
     * @return array{
     *   kimLau: array<string, mixed>,
     *   monthBenefit: array<string, mixed>,
     *   zodiac: array<string, mixed>,
     *   dates: list<array<string, mixed>>
     * }
     */
    public function run(PartnerDTO $bride, PartnerDTO $groom, TargetYearMonthDTO $target, string $eventType): array
    {
        $state = [
            'bride' => $bride,
            'groom' => $groom,
            'target' => $target,
            'eventType' => $eventType,
            'candidates' => [],
            'kimLau' => [],
            'monthBenefit' => [],
            'zodiac' => [],
        ];

        foreach ($this->filters as $filter) {
            $state = $filter->apply($state);
        }

        // Năm nhuận âm có thể làm phát sinh ngày lặp ở vài nguồn dữ liệu ngoài: luôn de-dup theo date.
        $unique = [];
        foreach ($state['candidates'] as $candidate) {
            $unique[$candidate['date']] = $candidate;
        }

        $dates = array_values($unique);
        usort($dates, static function (array $a, array $b): int {
            if ($a['score'] === $b['score']) {
                return strcmp($a['date'], $b['date']);
            }

            return $b['score'] <=> $a['score'];
        });

        return [
            'kimLau' => $state['kimLau'],
            'monthBenefit' => $state['monthBenefit'],
            'zodiac' => $state['zodiac'],
            'dates' => $dates,
        ];
    }
}
