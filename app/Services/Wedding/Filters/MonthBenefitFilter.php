<?php

declare(strict_types=1);

namespace App\Services\Wedding\Filters;

use App\Services\LunarCalendarService;
use Carbon\CarbonImmutable;

class MonthBenefitFilter implements WeddingFilterInterface
{
    /**
     * Tháng tiết khí ưu tiên theo tuổi cô dâu (theo Địa Chi năm sinh).
     *
     * @var array<string, array{dai: list<int>, tieu: list<int>}>
     */
    private const MONTH_BENEFIT_BY_CHI = [
        'Tý' => ['dai' => [6, 12], 'tieu' => [1, 7]],
        'Sửu' => ['dai' => [5, 11], 'tieu' => [4, 10]],
        'Dần' => ['dai' => [2, 8], 'tieu' => [3, 9]],
        'Mão' => ['dai' => [1, 7], 'tieu' => [4, 10]],
        'Thìn' => ['dai' => [4, 10], 'tieu' => [5, 11]],
        'Tỵ' => ['dai' => [3, 9], 'tieu' => [2, 8]],
        'Ngọ' => ['dai' => [6, 12], 'tieu' => [1, 7]],
        'Mùi' => ['dai' => [5, 11], 'tieu' => [6, 12]],
        'Thân' => ['dai' => [2, 8], 'tieu' => [3, 9]],
        'Dậu' => ['dai' => [1, 7], 'tieu' => [2, 8]],
        'Tuất' => ['dai' => [4, 10], 'tieu' => [5, 11]],
        'Hợi' => ['dai' => [3, 9], 'tieu' => [4, 10]],
    ];

    /**
     * term => [month, startDay, endDay, fallbackDay]
     *
     * @var array<string, array{0:int,1:int,2:int,3:int}>
     */
    private const TERM_WINDOWS = [
        'Lập Xuân' => [2, 3, 5, 4],
        'Kinh Trập' => [3, 4, 7, 6],
        'Thanh Minh' => [4, 4, 6, 5],
        'Lập Hạ' => [5, 4, 7, 6],
        'Mang Chủng' => [6, 4, 7, 6],
        'Tiểu Thử' => [7, 5, 8, 7],
        'Lập Thu' => [8, 6, 9, 7],
        'Bạch Lộ' => [9, 6, 9, 7],
        'Hàn Lộ' => [10, 7, 10, 8],
        'Lập Đông' => [11, 6, 9, 7],
        'Đại Tuyết' => [12, 6, 9, 7],
        'Tiểu Hàn' => [1, 4, 7, 5],
    ];

    public function __construct(
        private readonly LunarCalendarService $lunar,
    ) {}

    public function apply(array $state): array
    {
        $bride = $state['bride'];
        $target = $state['target'];
        $yearChi = $this->lunar->getCanChi($bride->birthDate)['yearChi'];
        $benefit = self::MONTH_BENEFIT_BY_CHI[$yearChi] ?? ['dai' => [2, 8], 'tieu' => [3, 9]];
        $referenceDate = $target->startDate()->addDays(14);
        $tietKhiMonth = $this->resolveTietKhiMonth($referenceDate);

        $tier = 'normal';
        if (in_array($tietKhiMonth, $benefit['dai'], true)) {
            $tier = 'dai-loi';
        } elseif (in_array($tietKhiMonth, $benefit['tieu'], true)) {
            $tier = 'tieu-loi';
        }

        $state['monthBenefit'] = [
            'brideYearChi' => $yearChi,
            'tietKhiMonth' => $tietKhiMonth,
            'daiLoiMonths' => $benefit['dai'],
            'tieuLoiMonths' => $benefit['tieu'],
            'tier' => $tier,
            'message' => $tier === 'dai-loi'
                ? "Tháng tiết khí {$tietKhiMonth} thuộc nhóm Đại Lợi cho tuổi {$yearChi}."
                : ($tier === 'tieu-loi'
                    ? "Tháng tiết khí {$tietKhiMonth} thuộc nhóm Tiểu Lợi cho tuổi {$yearChi}."
                    : "Tháng tiết khí {$tietKhiMonth} không thuộc Đại Lợi/Tiểu Lợi, nên lọc ngày kỹ hơn."),
        ];

        return $state;
    }

    public function resolveTietKhiMonth(CarbonImmutable $date): int
    {
        $lapXuanCurrentYear = $this->findSolarTermDate($date->year, 'Lập Xuân');
        $baseYear = $date->lessThan($lapXuanCurrentYear) ? $date->year - 1 : $date->year;

        $starts = [
            1 => $this->findSolarTermDate($baseYear, 'Lập Xuân'),
            2 => $this->findSolarTermDate($baseYear, 'Kinh Trập'),
            3 => $this->findSolarTermDate($baseYear, 'Thanh Minh'),
            4 => $this->findSolarTermDate($baseYear, 'Lập Hạ'),
            5 => $this->findSolarTermDate($baseYear, 'Mang Chủng'),
            6 => $this->findSolarTermDate($baseYear, 'Tiểu Thử'),
            7 => $this->findSolarTermDate($baseYear, 'Lập Thu'),
            8 => $this->findSolarTermDate($baseYear, 'Bạch Lộ'),
            9 => $this->findSolarTermDate($baseYear, 'Hàn Lộ'),
            10 => $this->findSolarTermDate($baseYear, 'Lập Đông'),
            11 => $this->findSolarTermDate($baseYear, 'Đại Tuyết'),
            12 => $this->findSolarTermDate($baseYear + 1, 'Tiểu Hàn'),
        ];

        $currentMonth = 1;
        foreach ($starts as $month => $startDate) {
            if ($date->greaterThanOrEqualTo($startDate)) {
                $currentMonth = $month;
            }
        }

        return $currentMonth;
    }

    public function findSolarTermDate(int $year, string $term): CarbonImmutable
    {
        [$month, $startDay, $endDay, $fallbackDay] = self::TERM_WINDOWS[$term];
        for ($day = $startDay; $day <= $endDay; $day++) {
            $date = CarbonImmutable::create($year, $month, $day, 0, 0, 0, 'Asia/Ho_Chi_Minh');
            if ($this->lunar->getSolarTerm($date) === $term) {
                return $date;
            }
        }

        return CarbonImmutable::create($year, $month, $fallbackDay, 0, 0, 0, 'Asia/Ho_Chi_Minh');
    }
}

