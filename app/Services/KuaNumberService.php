<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\CarbonImmutable;

class KuaNumberService
{
    private const EAST_GROUP = ['Khảm', 'Chấn', 'Tốn', 'Ly'];
    private const KUA_TO_CUNG = [
        1 => 'Khảm',
        2 => 'Khôn',
        3 => 'Chấn',
        4 => 'Tốn',
        6 => 'Càn',
        7 => 'Đoài',
        8 => 'Cấn',
        9 => 'Ly',
    ];

    public function __construct(
        private readonly LunarCalendarService $lunar,
    ) {}

    /**
     * @param 'male'|'female' $gender
     * @return array{
     *   birthDate: string,
     *   solarYear: int,
     *   lunarYearForFengShui: int,
     *   lapXuanDate: string,
     *   kuaNumber: int,
     *   cungPhi: string,
     *   group: string
     * }
     */
    public function analyze(string $gender, CarbonImmutable $birthDate): array
    {
        $solarYear = $birthDate->year;
        $lapXuan = $this->resolveLapXuanDate($solarYear);
        $lunarYearForFengShui = $birthDate->lessThanOrEqualTo($lapXuan)
            ? $solarYear - 1
            : $solarYear;

        $kuaNumber = $this->calculateKuaNumber($gender, $lunarYearForFengShui);
        $cungPhi = self::KUA_TO_CUNG[$kuaNumber] ?? 'Khảm';
        $group = in_array($cungPhi, self::EAST_GROUP, true) ? 'Đông Tứ Mệnh' : 'Tây Tứ Mệnh';

        return [
            'birthDate' => $birthDate->format('Y-m-d'),
            'solarYear' => $solarYear,
            'lunarYearForFengShui' => $lunarYearForFengShui,
            'lapXuanDate' => $lapXuan->format('Y-m-d'),
            'kuaNumber' => $kuaNumber,
            'cungPhi' => $cungPhi,
            'group' => $group,
        ];
    }

    /**
     * @param 'male'|'female' $gender
     */
    public function calculateKuaNumber(string $gender, int $lunarYear): int
    {
        $n = $this->reduceToSingleDigit((int) substr((string) $lunarYear, -2));

        if ($lunarYear >= 2000) {
            $kua = $gender === 'male' ? 9 - $n : $n + 6;
        } else {
            $kua = $gender === 'male' ? 10 - $n : $n + 5;
        }

        if ($kua <= 0) {
            $kua += 9;
        }

        $kua = $this->reduceToSingleDigit($kua);

        if ($kua === 5) {
            return $gender === 'male' ? 2 : 8;
        }

        return $kua;
    }

    private function reduceToSingleDigit(int $number): int
    {
        $number = abs($number);
        while ($number > 9) {
            $sum = 0;
            foreach (str_split((string) $number) as $digit) {
                $sum += (int) $digit;
            }
            $number = $sum;
        }

        return $number;
    }

    private function resolveLapXuanDate(int $year): CarbonImmutable
    {
        for ($day = 2; $day <= 6; $day++) {
            $date = CarbonImmutable::create($year, 2, $day, 0, 0, 0, 'Asia/Ho_Chi_Minh');
            if ($this->lunar->getSolarTerm($date) === 'Lập Xuân') {
                return $date;
            }
        }

        // Fallback thực tiễn khi không xác định được từ thuật toán tiết khí.
        return CarbonImmutable::create($year, 2, 4, 0, 0, 0, 'Asia/Ho_Chi_Minh');
    }
}

