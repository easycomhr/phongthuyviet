<?php

declare(strict_types=1);

namespace App\Services\Wedding\Filters;

use App\Services\LunarCalendarService;

class ZodiacClashFilter implements WeddingFilterInterface
{
    /** @var array<string, string> */
    private const CLASH = [
        'Tý' => 'Ngọ',
        'Sửu' => 'Mùi',
        'Dần' => 'Thân',
        'Mão' => 'Dậu',
        'Thìn' => 'Tuất',
        'Tỵ' => 'Hợi',
        'Ngọ' => 'Tý',
        'Mùi' => 'Sửu',
        'Thân' => 'Dần',
        'Dậu' => 'Mão',
        'Tuất' => 'Thìn',
        'Hợi' => 'Tỵ',
    ];

    public function __construct(
        private readonly LunarCalendarService $lunar,
    ) {}

    public function apply(array $state): array
    {
        $brideChi = $this->lunar->getCanChi($state['bride']->birthDate)['yearChi'];
        $groomChi = $this->lunar->getCanChi($state['groom']->birthDate)['yearChi'];
        $brideClash = self::CLASH[$brideChi] ?? '';
        $groomClash = self::CLASH[$groomChi] ?? '';

        $filtered = array_values(array_filter(
            $state['candidates'],
            static fn (array $candidate): bool => $candidate['dayChi'] !== $brideClash && $candidate['dayChi'] !== $groomClash,
        ));

        foreach ($filtered as &$candidate) {
            $candidate['reason'] .= sprintf(' Tránh ngày xung tuổi (%s/%s) đã được áp dụng.', $brideChi, $groomChi);
        }
        unset($candidate);

        $state['zodiac'] = [
            'brideYearChi' => $brideChi,
            'groomYearChi' => $groomChi,
            'brideClashDayChi' => $brideClash,
            'groomClashDayChi' => $groomClash,
        ];
        $state['candidates'] = $filtered;

        return $state;
    }
}

