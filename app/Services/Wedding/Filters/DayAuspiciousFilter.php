<?php

declare(strict_types=1);

namespace App\Services\Wedding\Filters;

use App\Services\FengShuiDailyService;
use App\Services\LunarCalendarService;

class DayAuspiciousFilter implements WeddingFilterInterface
{
    /** @var array<int, string> */
    private const SAT_CHU_BY_LUNAR_MONTH = [
        1 => 'Tỵ', 2 => 'Tý', 3 => 'Mùi', 4 => 'Dần',
        5 => 'Dậu', 6 => 'Thìn', 7 => 'Hợi', 8 => 'Ngọ',
        9 => 'Sửu', 10 => 'Thân', 11 => 'Mão', 12 => 'Tuất',
    ];

    /** @var list<string> */
    private const GOOD_TRUC = ['Khai', 'Thành', 'Định', 'Mãn'];

    /** @var list<string> */
    private const BAT_TUONG_CAN = ['Giáp', 'Bính', 'Mậu', 'Canh', 'Nhâm'];

    /** @var list<string> */
    private const BAT_TUONG_CHI = ['Tý', 'Sửu', 'Mão', 'Ngọ', 'Thân', 'Dậu'];

    /** @var array<string, string> */
    private const HOUR_RANGES = [
        'Tý' => '23:00-01:00',
        'Sửu' => '01:00-03:00',
        'Dần' => '03:00-05:00',
        'Mão' => '05:00-07:00',
        'Thìn' => '07:00-09:00',
        'Tỵ' => '09:00-11:00',
        'Ngọ' => '11:00-13:00',
        'Mùi' => '13:00-15:00',
        'Thân' => '15:00-17:00',
        'Dậu' => '17:00-19:00',
        'Tuất' => '19:00-21:00',
        'Hợi' => '21:00-23:00',
    ];

    public function __construct(
        private readonly LunarCalendarService $lunar,
        private readonly FengShuiDailyService $daily,
    ) {}

    public function apply(array $state): array
    {
        $target = $state['target'];
        $monthTier = $state['monthBenefit']['tier'] ?? 'normal';
        $eventType = (string) ($state['eventType'] ?? 'wedding');
        $eventLabel = $eventType === 'engagement' ? 'Ngày ăn hỏi' : 'Ngày rước dâu';
        $candidates = [];

        for ($date = $target->startDate(); $date->lessThanOrEqualTo($target->endDate()); $date = $date->addDay()) {
            $lunar = $this->lunar->solarToLunar($date);
            $canChi = $this->lunar->getCanChi($date);
            $xiu = $this->lunar->getXiuSao($date);
            $truc = $this->lunar->getTruc($date);
            $isTamNuong = $this->lunar->isTamNuong($date);
            $isNguyetKy = $this->lunar->isNguyetKy($date);
            $isSatChu = (self::SAT_CHU_BY_LUNAR_MONTH[$lunar[1]] ?? null) === $canChi['dayChi'];

            $isBatTuong = in_array($canChi['dayCan'], self::BAT_TUONG_CAN, true)
                && in_array($canChi['dayChi'], self::BAT_TUONG_CHI, true);
            $isHoangDao = in_array($truc, self::GOOD_TRUC, true) || $xiu['isLucky'];
            $dailyPayload = $this->daily->getDaily($date);

            if ($isTamNuong || $isNguyetKy || $isSatChu) {
                continue;
            }

            if (!$isBatTuong && !$isHoangDao) {
                continue;
            }

            $score = 62;
            if ($isBatTuong) {
                $score += 18;
            }
            if ($isHoangDao) {
                $score += 12;
            }
            if ($monthTier === 'dai-loi') {
                $score += 8;
            } elseif ($monthTier === 'tieu-loi') {
                $score += 4;
            }

            $badges = [];
            if ($isBatTuong) {
                $badges[] = 'Bất Tương';
            }
            if ($isHoangDao) {
                $badges[] = 'Hoàng Đạo';
            }
            if ($monthTier === 'dai-loi') {
                $badges[] = 'Tháng Đại Lợi';
            } elseif ($monthTier === 'tieu-loi') {
                $badges[] = 'Tháng Tiểu Lợi';
            }
            $badges[] = $eventType === 'engagement' ? 'Ăn hỏi' : 'Rước dâu';

            $luckyHourRanges = array_values(array_map(
                fn (array $h): string => sprintf('%s (%s)', $h['name'], self::HOUR_RANGES[$h['name']] ?? '--:--'),
                array_filter($dailyPayload['luckyHours'], static fn (array $h): bool => $h['isLucky']),
            ));
            $executionHours = array_slice($luckyHourRanges, 0, $eventType === 'engagement' ? 2 : 3);

            $candidates[] = [
                'date' => $date->format('Y-m-d'),
                'weekday' => $date->locale('vi')->translatedFormat('l'),
                'solarLabel' => $date->format('d/m/Y'),
                'lunarLabel' => sprintf('%02d/%02d/%d', $lunar[0], $lunar[1], $lunar[2]),
                'canChi' => $canChi['dayCan'].' '.$canChi['dayChi'],
                'score' => min(100, $score),
                'badges' => $badges,
                'reason' => $this->reasonText($isBatTuong, $isHoangDao, $truc, (string) $xiu['name']),
                'eventType' => $eventType,
                'eventLabel' => $eventLabel,
                'executionHours' => $executionHours,
                'dayChi' => $canChi['dayChi'],
            ];
        }

        $state['candidates'] = $candidates;

        return $state;
    }

    private function reasonText(bool $isBatTuong, bool $isHoangDao, string $truc, string $xiu): string
    {
        $parts = [];
        if ($isBatTuong) {
            $parts[] = 'Ngày có yếu tố Âm Dương Bất Tương, hợp cưới hỏi.';
        }
        if ($isHoangDao) {
            $parts[] = "Thuộc ngày Hoàng Đạo theo Trực {$truc} / Tú {$xiu}.";
        }
        if (!$parts) {
            $parts[] = 'Ngày khá ổn định, có thể cân nhắc với giờ lành.';
        }

        return implode(' ', $parts);
    }
}
