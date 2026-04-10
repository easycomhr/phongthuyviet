<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\CarbonImmutable;

/**
 * Computes Vietnamese feng-shui daily information for a given solar date.
 *
 * Wraps LunarCalendarService and adds:
 *  - Giờ Hoàng Đạo / Hắc Đạo  (12 two-hour periods, 6 lucky / 6 unlucky)
 *  - Tuổi xung khắc            (zodiac signs that conflict with today's day Chi)
 *  - Hướng Hỷ Thần / Tài Thần  (auspicious directions based on day's Can)
 */
class FengShuiDailyService
{
    private const CHI = ['Tý', 'Sửu', 'Dần', 'Mão', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tuất', 'Hợi'];

    private const WEEKDAYS = [
        0 => 'Chủ Nhật',
        1 => 'Thứ Hai',
        2 => 'Thứ Ba',
        3 => 'Thứ Tư',
        4 => 'Thứ Năm',
        5 => 'Thứ Sáu',
        6 => 'Thứ Bảy',
    ];

    /**
     * Twelve spirits of the "Lục Thập Hoa Giáp" hour cycle.
     * Index 0 = Thanh Long, 1 = Minh Đường, …
     * true  = Hoàng Đạo (auspicious)
     * false = Hắc Đạo  (inauspicious)
     */
    private const SPIRIT_LUCKY = [true, true, false, false, true, true, false, true, false, true, false, false];

    /**
     * Hỷ Thần (Joy God) direction indexed by day Can (0=Giáp … 9=Quý).
     */
    private const HAPPINESS_DIR = [
        'Đông Bắc', // Giáp
        'Đông Nam', // Ất
        'Tây Nam',  // Bính
        'Tây Bắc',  // Đinh
        'Tây Bắc',  // Mậu
        'Đông Nam', // Kỷ
        'Tây Nam',  // Canh
        'Đông Bắc', // Tân
        'Bắc',      // Nhâm
        'Nam',      // Quý
    ];

    /**
     * Tài Thần (Wealth God) direction indexed by day Can (0=Giáp … 9=Quý).
     */
    private const WEALTH_DIR = [
        'Bắc',      // Giáp
        'Bắc',      // Ất
        'Nam',      // Bính
        'Nam',      // Đinh
        'Đông',     // Mậu
        'Tây Nam',  // Kỷ
        'Tây Bắc',  // Canh
        'Tây Bắc',  // Tân
        'Đông Bắc', // Nhâm
        'Đông',     // Quý
    ];

    public function __construct(
        private readonly LunarCalendarService $lunar
    ) {}

    /**
     * Build the full daily feng-shui payload for the given solar date.
     *
     * @return array{
     *   solar: array{day: int, month: int, year: int, weekday: string},
     *   lunar: array{day: int, month: int, year: int, isLeapMonth: bool},
     *   canChi: array{dayCan: string, dayChi: string, monthCan: string, monthChi: string, yearCan: string, yearChi: string},
     *   solarTerm: string|null,
     *   luckyHours: list<array{name: string, isLucky: bool}>,
     *   conflictZodiacs: list<string>,
     *   directions: array{happiness: string, wealth: string}
     * }
     */
    public function getDaily(CarbonImmutable $date): array
    {
        $lunarDate = $this->lunar->solarToLunar($date);
        $canChi    = $this->lunar->getCanChi($date);
        $solarTerm = $this->lunar->getSolarTerm($date);

        return [
            'solar'          => $this->buildSolar($date),
            'lunar'          => $this->buildLunar($lunarDate),
            'canChi'         => $canChi,
            'solarTerm'      => $solarTerm,
            'luckyHours'     => $this->buildLuckyHours($canChi['dayChi']),
            'conflictZodiacs'=> $this->buildConflictZodiacs($canChi['dayChi']),
            'directions'     => $this->buildDirections($canChi['dayCan']),
        ];
    }

    // ── Private builders ──────────────────────────────────────────────────────

    private function buildSolar(CarbonImmutable $date): array
    {
        return [
            'day'     => $date->day,
            'month'   => $date->month,
            'year'    => $date->year,
            'weekday' => self::WEEKDAYS[$date->dayOfWeek],
        ];
    }

    /** @param array{0: int, 1: int, 2: int, 3: bool} $lunarDate */
    private function buildLunar(array $lunarDate): array
    {
        return [
            'day'         => $lunarDate[0],
            'month'       => $lunarDate[1],
            'year'        => $lunarDate[2],
            'isLeapMonth' => $lunarDate[3],
        ];
    }

    /**
     * Build the 12 two-hour period (giờ Chi) slots with their Hoàng/Hắc Đạo status.
     *
     * The 12 spirits (Thập Nhị Thần) rotate by 2 positions for each step in
     * the day Chi cycle:
     *   startPos = (dayChiIdx * 2) % 12
     * Hour Chi i → spirit index = (i - startPos + 12) % 12
     *
     * @return list<array{name: string, isLucky: bool}>
     */
    private function buildLuckyHours(string $dayChi): array
    {
        $dayChiIdx = array_search($dayChi, self::CHI, true);
        $startPos  = ($dayChiIdx * 2) % 12;

        $hours = [];
        for ($i = 0; $i < 12; $i++) {
            $spiritIdx = ($i - $startPos + 12) % 12;
            $hours[]   = [
                'name'    => self::CHI[$i],
                'isLucky' => self::SPIRIT_LUCKY[$spiritIdx],
            ];
        }

        return $hours;
    }

    /**
     * Return the zodiac Chi names that conflict with today's day Chi.
     * Primary conflict: Chi diametrically opposite (+ 6 positions).
     *
     * @return list<string>
     */
    private function buildConflictZodiacs(string $dayChi): array
    {
        $idx            = array_search($dayChi, self::CHI, true);
        $conflictIdx    = ($idx + 6) % 12;

        return [self::CHI[$conflictIdx]];
    }

    /**
     * Return the auspicious directions for the day based on the day's Can.
     *
     * @return array{happiness: string, wealth: string}
     */
    private function buildDirections(string $dayCan): array
    {
        $can = ['Giáp', 'Ất', 'Bính', 'Đinh', 'Mậu', 'Kỷ', 'Canh', 'Tân', 'Nhâm', 'Quý'];
        $idx = array_search($dayCan, $can, true);

        return [
            'happiness' => self::HAPPINESS_DIR[$idx],
            'wealth'    => self::WEALTH_DIR[$idx],
        ];
    }
}
