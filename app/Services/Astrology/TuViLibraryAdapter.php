<?php

declare(strict_types=1);

namespace App\Services\Astrology;

use App\Contracts\AstrologyEngineInterface;
use App\DTO\Astrology\ChartDTO;
use App\DTO\Astrology\PalaceDTO;
use App\DTO\Astrology\StarDTO;
use App\Services\LunarCalendarService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

class TuViLibraryAdapter implements AstrologyEngineInterface
{
    private const STEM_TO_ELEMENT = [
        'Giáp' => 'moc', 'Ất' => 'moc',
        'Bính' => 'hoa', 'Đinh' => 'hoa',
        'Mậu' => 'tho', 'Kỷ' => 'tho',
        'Canh' => 'kim', 'Tân' => 'kim',
        'Nhâm' => 'thuy', 'Quý' => 'thuy',
    ];

    private const BRANCH_TO_ELEMENT = [
        'Tý' => 'thuy', 'Sửu' => 'tho', 'Dần' => 'moc', 'Mão' => 'moc',
        'Thìn' => 'tho', 'Tỵ' => 'hoa', 'Ngọ' => 'hoa', 'Mùi' => 'tho',
        'Thân' => 'kim', 'Dậu' => 'kim', 'Tuất' => 'tho', 'Hợi' => 'thuy',
    ];

    private const DAY_CAN_INDEX = [
        'Giáp' => 0, 'Ất' => 1, 'Bính' => 2, 'Đinh' => 3, 'Mậu' => 4,
        'Kỷ' => 5, 'Canh' => 6, 'Tân' => 7, 'Nhâm' => 8, 'Quý' => 9,
    ];

    private const HOUR_CHI_ORDER = ['Tý', 'Sửu', 'Dần', 'Mão', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tuất', 'Hợi'];

    private const PALACE_NAMES = [
        'Mệnh', 'Phụ Mẫu', 'Phúc Đức', 'Điền Trạch', 'Quan Lộc', 'Nô Bộc',
        'Thiên Di', 'Tật Ách', 'Tài Bạch', 'Tử Tức', 'Phu Thê', 'Huynh Đệ',
    ];

    private const MENH_BY_CHI = [
        'Tý' => 'Kim Bạch Kim',
        'Sửu' => 'Giản Hạ Thủy',
        'Dần' => 'Lư Trung Hỏa',
        'Mão' => 'Đại Khê Thủy',
        'Thìn' => 'Phú Đăng Hỏa',
        'Tỵ' => 'Sa Trung Thổ',
        'Ngọ' => 'Thiên Thượng Hỏa',
        'Mùi' => 'Sa Trung Kim',
        'Thân' => 'Kiếm Phong Kim',
        'Dậu' => 'Sơn Hạ Hỏa',
        'Tuất' => 'Ốc Thượng Thổ',
        'Hợi' => 'Đại Hải Thủy',
    ];

    public function __construct(
        private readonly LunarCalendarService $lunar,
    ) {}

    public function calculateChart(Carbon $birthDate, string $gender): array
    {
        $raw = $this->queryThirdPartyLibrary($birthDate, $gender);
        $dto = $this->mapRawToDto($raw);

        return $dto->toArray();
    }

    public function getLunarDate(Carbon $solarDate): array
    {
        $immutable = CarbonImmutable::create(
            $solarDate->year,
            $solarDate->month,
            $solarDate->day,
            0,
            0,
            0,
            'Asia/Ho_Chi_Minh',
        );
        [$day, $month, $year, $isLeap] = $this->lunar->solarToLunar($immutable);

        return [
            'lunar_day' => $day,
            'lunar_month' => $month,
            'lunar_year' => $year,
            'is_leap_month' => $isLeap,
        ];
    }

    public function calculateBaziProfile(Carbon $birthDate): array
    {
        $date = CarbonImmutable::create(
            $birthDate->year,
            $birthDate->month,
            $birthDate->day,
            $birthDate->hour,
            $birthDate->minute,
            0,
            'Asia/Ho_Chi_Minh',
        );
        $canChi = $this->lunar->getCanChi($date);
        $hourChi = $this->resolveHourChi($date->hour);
        $hourCan = $this->resolveHourCan($canChi['dayCan'], $hourChi);

        $pillars = [
            'year' => $this->buildPillar($canChi['yearCan'], $canChi['yearChi']),
            'month' => $this->buildPillar($canChi['monthCan'], $canChi['monthChi']),
            'day' => $this->buildPillar($canChi['dayCan'], $canChi['dayChi']),
            'hour' => $this->buildPillar($hourCan, $hourChi),
        ];

        $count = ['kim' => 0, 'moc' => 0, 'thuy' => 0, 'hoa' => 0, 'tho' => 0];
        foreach ($pillars as $pillar) {
            $count[$pillar['can_element']]++;
            $count[$pillar['chi_element']]++;
        }

        $max = max($count);
        $dominant = (string) array_key_first(array_filter($count, static fn (int $v): bool => $v === $max));
        $missing = array_keys(array_filter($count, static fn (int $v): bool => $v === 0));

        return [
            'pillars' => $pillars,
            'element_count' => $count,
            'dominant_element' => $dominant,
            'missing_elements' => array_values($missing),
        ];
    }

    /**
     * Mô phỏng dữ liệu trả về từ thư viện bên thứ 3 (cấu trúc lộn xộn).
     * Trong thực tế chỉ thay thân hàm này bằng call thật tới lib ngoài.
     *
     * @return array<string, mixed>
     */
    private function queryThirdPartyLibrary(Carbon $birthDate, string $gender): array
    {
        $immutable = CarbonImmutable::create(
            $birthDate->year,
            $birthDate->month,
            $birthDate->day,
            0,
            0,
            0,
            'Asia/Ho_Chi_Minh',
        );
        [$lunarDay, $lunarMonth, $lunarYear, $isLeap] = $this->lunar->solarToLunar($immutable);
        $canChi = $this->lunar->getCanChi($immutable);
        $seed = (int) ($birthDate->format('Ymd') . ($gender === 'male' ? '1' : '2'));
        $chi = $canChi['yearChi'];

        $palaces = [];
        foreach (self::PALACE_NAMES as $i => $name) {
            $score = 55 + (($seed + $i * 17) % 46); // 55..100
            $palaces[] = [
                'name' => $name,
                'idx' => $i + 1,
                'point' => $score,
                'text' => $this->palaceSummary($name, $score),
                'star_set' => [
                    ['n' => $this->starName($seed, $i, 0), 'q' => $score >= 78 ? 'Cát tinh' : 'Bình hòa', 'el' => $this->starElement($seed + $i)],
                    ['n' => $this->starName($seed, $i, 1), 'q' => $score < 68 ? 'Hung tinh' : 'Phụ tinh', 'el' => $this->starElement($seed + $i + 1)],
                ],
            ];
        }

        return [
            'profile' => (object) [
                'birth_date' => $birthDate->format('Y-m-d'),
                'gender' => $gender,
                'can_chi_year' => $canChi['yearCan'].' '.$canChi['yearChi'],
                'can_chi_day' => $canChi['dayCan'].' '.$canChi['dayChi'],
                'lunar' => sprintf('%02d/%02d/%d%s', $lunarDay, $lunarMonth, $lunarYear, $isLeap ? ' (Nhuận)' : ''),
                'menh' => self::MENH_BY_CHI[$chi] ?? 'Bình mệnh',
                'cuc' => $gender === 'male' ? 'Mộc Tam Cục' : 'Thủy Nhị Cục',
            ],
            'palaces' => $palaces,
        ];
    }

    /**
     * @param array<string, mixed> $raw
     */
    private function mapRawToDto(array $raw): ChartDTO
    {
        /** @var object $profile */
        $profile = $raw['profile'];
        $profileMapped = [
            'birthDate' => (string) $profile->birth_date,
            'gender' => $profile->gender === 'male' ? 'Nam' : 'Nữ',
            'canChiYear' => (string) $profile->can_chi_year,
            'canChiDay' => (string) $profile->can_chi_day,
            'lunarDateLabel' => (string) $profile->lunar,
            'menh' => (string) $profile->menh,
            'cuc' => (string) $profile->cuc,
        ];

        $palaces = [];
        foreach ($raw['palaces'] as $palace) {
            $stars = [];
            foreach ($palace['star_set'] as $star) {
                $stars[] = new StarDTO(
                    (string) $star['n'],
                    (string) $star['q'],
                    (string) $star['el'],
                );
            }

            $palaces[] = new PalaceDTO(
                (string) $palace['name'],
                (int) $palace['idx'],
                (int) $palace['point'],
                (string) $palace['text'],
                $stars,
            );
        }

        return new ChartDTO($profileMapped, $palaces);
    }

    private function palaceSummary(string $palaceName, int $score): string
    {
        if ($score >= 85) {
            return "Cung {$palaceName} cát khí nổi trội, thuận lợi phát triển dài hạn.";
        }

        if ($score >= 70) {
            return "Cung {$palaceName} ổn định, cần giữ nhịp sinh hoạt điều độ để tăng vận.";
        }

        return "Cung {$palaceName} có dao động, nên ưu tiên phòng ngừa rủi ro và tích đức.";
    }

    private function starName(int $seed, int $palaceIndex, int $offset): string
    {
        $names = ['Tử Vi', 'Thiên Phủ', 'Thái Dương', 'Thái Âm', 'Vũ Khúc', 'Thiên Đồng', 'Liêm Trinh', 'Thiên Tướng', 'Tham Lang', 'Phá Quân'];
        $idx = ($seed + $palaceIndex * 3 + $offset * 5) % count($names);

        return $names[$idx];
    }

    private function starElement(int $seed): string
    {
        $elements = ['Kim', 'Mộc', 'Thủy', 'Hỏa', 'Thổ'];
        return $elements[$seed % 5];
    }

    /**
     * @return array{can: string, chi: string, can_element: string, chi_element: string}
     */
    private function buildPillar(string $can, string $chi): array
    {
        return [
            'can' => $can,
            'chi' => $chi,
            'can_element' => self::STEM_TO_ELEMENT[$can] ?? 'tho',
            'chi_element' => self::BRANCH_TO_ELEMENT[$chi] ?? 'tho',
        ];
    }

    private function resolveHourChi(int $hour): string
    {
        if ($hour >= 23 || $hour < 1) {
            return 'Tý';
        }

        $index = intdiv($hour + 1, 2);
        return self::HOUR_CHI_ORDER[$index] ?? 'Tý';
    }

    private function resolveHourCan(string $dayCan, string $hourChi): string
    {
        $canOrder = array_keys(self::DAY_CAN_INDEX);
        $dayIndex = self::DAY_CAN_INDEX[$dayCan] ?? 0;
        $hourIndex = array_search($hourChi, self::HOUR_CHI_ORDER, true);
        $start = ($dayIndex % 5) * 2;
        $canIndex = ($start + (int) $hourIndex) % 10;

        return $canOrder[$canIndex] ?? 'Giáp';
    }
}
