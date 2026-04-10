<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\CarbonImmutable;

/**
 * Computes a scored list of days for a given month, filtered by event category.
 *
 * Scoring model (0–100):
 *  Base 40
 *  +20  Trực tốt (Kiến, Trừ, Mãn, Định, Thành, Khai)
 *  +20  Tú Sao tốt (xiuSao.isLucky)
 *  −30  Nguyệt Kỵ  (âm ngày 5, 14, 23)
 *  Force isGood=false  Tam Nương (âm ngày 3, 7, 13, 18, 22, 26)
 *  isGood = score >= 60 && !isTamNuong
 */
class GoodDayCalculatorService
{
    /** Trực names considered auspicious for most event categories. */
    private const GOOD_TRUC = ['Kiến', 'Trừ', 'Mãn', 'Định', 'Thành', 'Khai'];

    public function __construct(
        private readonly LunarCalendarService $lunar,
        private readonly FengShuiDailyService $fengShui,
    ) {}

    /**
     * Return scored daily data for every day in the given month.
     *
     * @return list<array{
     *   date: string,
     *   solar: array{day: int, month: int, year: int, weekday: string},
     *   lunar: array{day: int, month: int, year: int, isLeapMonth: bool},
     *   canChi: array{dayCan: string, dayChi: string, monthCan: string, monthChi: string, yearCan: string, yearChi: string},
     *   truc: string,
     *   xiuSao: array{name: string, isLucky: bool},
     *   score: int,
     *   isGood: bool,
     *   isTamNuong: bool,
     *   isNguyetKy: bool,
     *   luckyHours: list<array{name: string, isLucky: bool}>
     * }>
     */
    public function getMonthDays(string $categorySlug, int $year, int $month): array
    {
        $daysInMonth = CarbonImmutable::create($year, $month, 1, 0, 0, 0, 'Asia/Ho_Chi_Minh')
            ->daysInMonth;

        $result = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date      = CarbonImmutable::create($year, $month, $day, 0, 0, 0, 'Asia/Ho_Chi_Minh');
            $daily     = $this->fengShui->getDaily($date);
            $truc      = $this->lunar->getTruc($date);
            $xiuSao    = $this->lunar->getXiuSao($date);
            $isTam     = $this->lunar->isTamNuong($date);
            $isNguyetKy = $this->lunar->isNguyetKy($date);

            $score = $this->computeScore($truc, $xiuSao['isLucky'], $isTam, $isNguyetKy, $categorySlug);

            $result[] = [
                'date'        => $date->format('Y-m-d'),
                'solar'       => $daily['solar'],
                'lunar'       => $daily['lunar'],
                'canChi'      => $daily['canChi'],
                'truc'        => $truc,
                'xiuSao'      => $xiuSao,
                'score'       => $score,
                'isGood'      => !$isTam && $score >= 60,
                'isTamNuong'  => $isTam,
                'isNguyetKy'  => $isNguyetKy,
                'luckyHours'  => $daily['luckyHours'],
            ];
        }

        return $result;
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function computeScore(
        string $truc,
        bool   $xiuLucky,
        bool   $isTamNuong,
        bool   $isNguyetKy,
        string $categorySlug,
    ): int {
        if ($isTamNuong) {
            return 0;
        }

        $score = 40;

        if (in_array($truc, self::GOOD_TRUC, true)) {
            $score += 20;
        }

        if ($xiuLucky) {
            $score += 20;
        }

        if ($isNguyetKy) {
            $score -= 30;
        }

        return max(0, min(100, $score));
    }
}
