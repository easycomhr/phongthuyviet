<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\CarbonImmutable;

/**
 * Vietnamese Lunar Calendar Service.
 *
 * Implements the Hồ Ngọc Đức algorithm for Solar ↔ Lunar conversion,
 * Can Chi calculation, and Solar Term (Tiết Khí) detection.
 * All computations use timezone Asia/Ho_Chi_Minh (UTC+7).
 */
class LunarCalendarService
{
    private const UTC_OFFSET = 7;

    private const CAN = ['Giáp', 'Ất', 'Bính', 'Đinh', 'Mậu', 'Kỷ', 'Canh', 'Tân', 'Nhâm', 'Quý'];
    private const CHI = ['Tý', 'Sửu', 'Dần', 'Mão', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tuất', 'Hợi'];

    /**
     * Solar term name keyed by the ecliptic degree (multiples of 15) at which it begins.
     * 315° = Lập Xuân, 330° = Vũ Thủy, …
     */
    private const SOLAR_TERMS = [
        315 => 'Lập Xuân',  330 => 'Vũ Thủy',    345 => 'Kinh Trập',
          0 => 'Xuân Phân',  15 => 'Thanh Minh',   30 => 'Cốc Vũ',
         45 => 'Lập Hạ',    60 => 'Tiểu Mãn',     75 => 'Mang Chủng',
         90 => 'Hạ Chí',   105 => 'Tiểu Thử',    120 => 'Đại Thử',
        135 => 'Lập Thu',  150 => 'Xử Thử',      165 => 'Bạch Lộ',
        180 => 'Thu Phân', 195 => 'Hàn Lộ',      210 => 'Sương Giáng',
        225 => 'Lập Đông', 240 => 'Tiểu Tuyết',  255 => 'Đại Tuyết',
        270 => 'Đông Chí', 285 => 'Tiểu Hàn',    300 => 'Đại Hàn',
    ];

    // ── Public API ────────────────────────────────────────────────────────────

    /**
     * Convert a solar date to its Vietnamese lunar date.
     *
     * @return array{0: int, 1: int, 2: int, 3: bool}  [lunarDay, lunarMonth, lunarYear, isLeapMonth]
     */
    public function solarToLunar(CarbonImmutable $date): array
    {
        return $this->convertSolar2Lunar($date->day, $date->month, $date->year, self::UTC_OFFSET);
    }

    /**
     * Return the Can Chi strings for day, month, and year of the given solar date.
     *
     * @return array{dayCan: string, dayChi: string, monthCan: string, monthChi: string, yearCan: string, yearChi: string}
     */
    public function getCanChi(CarbonImmutable $date): array
    {
        $jd    = $this->jdFromDate($date->day, $date->month, $date->year);
        $lunar = $this->solarToLunar($date);
        [, $lunarMonth, $lunarYear] = $lunar;

        // Year Can/Chi — epoch anchor: year 4 CE = Giáp Tý
        $yearCanIdx = (($lunarYear - 4) % 10 + 10) % 10;
        $yearChiIdx = (($lunarYear - 4) % 12 + 12) % 12;

        // Day Can/Chi — derived from Julian Day Number
        $dayCanIdx = ($jd + 9) % 10;
        $dayChiIdx = ($jd + 1) % 12;

        // Month Can — year's Can mod 5 determines the starting Can for month 1 (Dần)
        // Giáp/Kỷ(0/5)→Bính(2), Ất/Canh(1/6)→Mậu(4), Bính/Tân(2/7)→Canh(6),
        // Đinh/Nhâm(3/8)→Nhâm(8), Mậu/Quý(4/9)→Giáp(0)
        $startMonthCan = ($yearCanIdx % 5) * 2 + 2;
        $monthCanIdx   = ($startMonthCan + ($lunarMonth - 1) * 2) % 10;

        // Month Chi — month 1 = Dần(2), month 2 = Mão(3), …
        $monthChiIdx = ($lunarMonth + 1) % 12;

        return [
            'dayCan'   => self::CAN[$dayCanIdx],
            'dayChi'   => self::CHI[$dayChiIdx],
            'monthCan' => self::CAN[$monthCanIdx],
            'monthChi' => self::CHI[$monthChiIdx],
            'yearCan'  => self::CAN[$yearCanIdx],
            'yearChi'  => self::CHI[$yearChiIdx],
        ];
    }

    /**
     * Return the Vietnamese solar term name if the given day is the first day
     * of a new solar term, or null otherwise.
     */
    public function getSolarTerm(CarbonImmutable $date): ?string
    {
        $jd = $this->jdFromDate($date->day, $date->month, $date->year);

        $todayBucket     = (int) floor($this->getSunLongitude($jd, self::UTC_OFFSET) / 15);
        $yesterdayBucket = (int) floor($this->getSunLongitude($jd - 1, self::UTC_OFFSET) / 15);

        if ($todayBucket === $yesterdayBucket) {
            return null;
        }

        $degree = ($todayBucket * 15) % 360;

        return self::SOLAR_TERMS[$degree] ?? null;
    }

    /**
     * 12 Trực (Officers) in order: Kiến → Bế.
     * isGoodTruc[i] = true if that Trực is auspicious for general use.
     */
    private const TRUC = ['Kiến', 'Trừ', 'Mãn', 'Bình', 'Định', 'Chấp', 'Phá', 'Nguy', 'Thành', 'Thu', 'Khai', 'Bế'];

    /**
     * 28 Nhị Thập Bát Tú (Lunar Mansions) in order.
     * Calibrated: JD 2451549 (Jan 5.5, 2000) = Giốc (idx 0).
     * XIU_JD_OFFSET = (0 − 2451549 % 28 + 28) % 28 = (0 − 9 + 28) % 28 = 19.
     * Verify against lịch vạn niên before production use.
     */
    private const XIU_NAMES = [
        'Giốc', 'Cang', 'Đê',   'Phòng', 'Tâm',   'Vĩ',    'Cơ',
        'Đẩu',  'Ngưu', 'Nữ',   'Hư',    'Nguy',   'Thất',  'Bích',
        'Khuê', 'Lâu',  'Vị',   'Mão',   'Tất',    'Chủy',  'Sâm',
        'Tỉnh', 'Quỷ',  'Liễu', 'Tinh',  'Trương', 'Dực',   'Chẩn',
    ];

    /** Lucky Tú flags indexed by XIU_NAMES order. */
    private const XIU_LUCKY = [
        true,  false, true,  true,  false, true,  true,  // Giốc…Cơ
        true,  false, false, false, false, true,  true,  // Đẩu…Bích
        false, true,  true,  true,  true,  false, false, // Khuê…Sâm
        true,  false, false, false, true,  false, true,  // Tỉnh…Chẩn
    ];

    private const XIU_JD_OFFSET = 19;

    // ── Public API (extensions) ───────────────────────────────────────────────

    /**
     * Return the Trực (one of 12 Officers) for the given solar date.
     * Formula: trucIdx = (dayChiIdx − monthChiIdx + 12) % 12
     * where dayChiIdx = (jd + 1) % 12 and monthChiIdx = (lunarMonth + 1) % 12.
     */
    public function getTruc(CarbonImmutable $date): string
    {
        $jd            = $this->jdFromDate($date->day, $date->month, $date->year);
        $dayChiIdx     = ($jd + 1) % 12;
        $lunarMonth    = $this->solarToLunar($date)[1];
        $monthChiIdx   = ($lunarMonth + 1) % 12;
        $trucIdx       = ($dayChiIdx - $monthChiIdx + 12) % 12;

        return self::TRUC[$trucIdx];
    }

    /**
     * Return true if the given solar date falls on a Tam Nương day
     * (lunar days 3, 7, 13, 18, 22, 26).
     */
    public function isTamNuong(CarbonImmutable $date): bool
    {
        $lunarDay = $this->solarToLunar($date)[0];

        return in_array($lunarDay, [3, 7, 13, 18, 22, 26], true);
    }

    /**
     * Return true if the given solar date falls on a Nguyệt Kỵ day
     * (lunar days 5, 14, 23).
     */
    public function isNguyetKy(CarbonImmutable $date): bool
    {
        $lunarDay = $this->solarToLunar($date)[0];

        return in_array($lunarDay, [5, 14, 23], true);
    }

    /**
     * Return the Nhị Thập Bát Tú (28 Lunar Mansion) for the given solar date.
     *
     * @return array{name: string, isLucky: bool}
     */
    public function getXiuSao(CarbonImmutable $date): array
    {
        $jd      = $this->jdFromDate($date->day, $date->month, $date->year);
        $xiuIdx  = ($jd + self::XIU_JD_OFFSET) % 28;

        return [
            'name'    => self::XIU_NAMES[$xiuIdx],
            'isLucky' => self::XIU_LUCKY[$xiuIdx],
        ];
    }

    // ── JDN helpers (public so tests can do round-trip checks) ────────────────

    /** Convert a Gregorian (proleptic) date to its Julian Day Number. */
    public function jdFromDate(int $dd, int $mm, int $yy): int
    {
        $a  = (int) floor((14 - $mm) / 12);
        $y  = $yy + 4800 - $a;
        $m  = $mm + 12 * $a - 3;
        $jd = $dd
            + (int) floor((153 * $m + 2) / 5)
            + 365 * $y
            + (int) floor($y / 4)
            - (int) floor($y / 100)
            + (int) floor($y / 400)
            - 32045;

        if ($jd < 2299161) {
            // Julian calendar
            $jd = $dd
                + (int) floor((153 * $m + 2) / 5)
                + 365 * $y
                + (int) floor($y / 4)
                - 32083;
        }

        return $jd;
    }

    /** Convert a Julian Day Number back to [day, month, year]. */
    public function dateFromJd(int $jd): array
    {
        $a  = $jd + 32044;
        $b  = (int) floor((4 * $a + 3) / 146097);
        $c  = $a - (int) floor(146097 * $b / 4);
        $d  = (int) floor((4 * $c + 3) / 1461);
        $e  = $c - (int) floor(1461 * $d / 4);
        $m  = (int) floor((5 * $e + 2) / 153);
        $dd = $e - (int) floor((153 * $m + 2) / 5) + 1;
        $mm = $m + 3 - 12 * (int) floor($m / 10);
        $yy = 100 * $b + $d - 4800 + (int) floor($m / 10);

        return [$dd, $mm, $yy];
    }

    // ── Astronomical core ─────────────────────────────────────────────────────

    /** Sun's apparent ecliptic longitude in degrees at Julian Day $jd. */
    private function sunLongitude(float $jd): float
    {
        $dr = M_PI / 180;
        $T  = ($jd - 2451545.0) / 36525;
        $T2 = $T * $T;

        $M  = 357.52910 + 35999.05030 * $T - 0.0001559 * $T2 - 0.00000048 * $T * $T2;
        $L0 = 280.46646 + 36000.76983 * $T + 0.0003032 * $T2;

        $DL  = (1.914600 - 0.004817 * $T - 0.000014 * $T2) * sin($dr * $M);
        $DL += (0.019993 - 0.000101 * $T) * sin($dr * 2 * $M);
        $DL += 0.000290 * sin($dr * 3 * $M);

        $L = $L0 + $DL;
        $L -= 20.4922 / 3600; // aberration

        return $L - 360 * (int) floor($L / 360);
    }

    /** Sun's longitude at the start of day $dayNumber in timezone $tz (hours east). */
    private function getSunLongitude(int $dayNumber, float $tz): float
    {
        return $this->sunLongitude($dayNumber - 0.5 - $tz / 24);
    }

    /** Julian Day Number (float) of the k-th new moon after Jan 0.5, 1900. */
    private function newMoon(int $k): float
    {
        $dr  = M_PI / 180;
        $T   = $k / 1236.85;
        $T2  = $T * $T;
        $T3  = $T2 * $T;
        $Jd1 = 2415020.75933
            + 29.53058868 * $k
            + 0.0001178 * $T2
            - 0.000000155 * $T3
            + 0.00033 * sin((166.56 + 132.87 * $T - 0.009173 * $T2) * $dr);

        $M   = 359.2242 + 29.10535608 * $k - 0.0000333 * $T2 - 0.00000347 * $T3;
        $Mpr = 306.0253 + 385.81691806 * $k + 0.0107306 * $T2 + 0.00001236 * $T3;
        $F   = 21.2964 + 390.67050646 * $k - 0.0016528 * $T2 - 0.00000239 * $T3;

        $C1  = (0.1734 - 0.000393 * $T) * sin($M * $dr)
            +  0.0021  * sin(2 * $dr * $M)
            -  0.4068  * sin($Mpr * $dr)
            +  0.0161  * sin(2 * $dr * $Mpr)
            -  0.0004  * sin(3 * $dr * $Mpr)
            +  0.0104  * sin(2 * $dr * $F)
            -  0.0051  * sin($dr * ($M + $Mpr))
            -  0.0074  * sin($dr * ($M - $Mpr))
            +  0.0004  * sin($dr * (2 * $F + $M))
            -  0.0004  * sin($dr * (2 * $F - $M))
            -  0.0006  * sin($dr * (2 * $F + $Mpr))
            +  0.0010  * sin($dr * (2 * $F - $Mpr))
            +  0.0005  * sin($dr * ($M + 2 * $Mpr));

        $deltat = ($T < -11)
            ? 0.001 + 0.000839 * $T + 0.0002261 * $T2 - 0.00000845 * $T3 - 0.000000081 * $T * $T3
            : -0.000278 + 0.000265 * $T + 0.000262 * $T2;

        return $Jd1 + $C1 - $deltat;
    }

    /** Day number (integer JDN) of the k-th new moon in timezone $tz. */
    private function getNewMoonDay(int $k, float $tz): int
    {
        return (int) floor($this->newMoon($k) + 0.5 + $tz / 24);
    }

    /**
     * JDN of the start of the 11th lunar month (tháng 11 âm, i.e. the month
     * containing the Winter Solstice) for solar year $yy.
     */
    private function getLunarMonth11(int $yy, float $tz): int
    {
        $off  = $this->jdFromDate(31, 12, $yy) - 2415021;
        $k    = (int) floor($off / 29.530588853);
        $nm   = $this->getNewMoonDay($k, $tz);

        if ((int) floor($this->getSunLongitude($nm, $tz) / 30) >= 9) {
            $nm = $this->getNewMoonDay($k - 1, $tz);
        }

        return $nm;
    }

    /**
     * Offset (number of months from a11) of the leap month in the
     * lunar year starting at JDN $a11.
     */
    private function getLeapMonthOffset(int $a11, float $tz): int
    {
        $k    = (int) floor(($a11 - 2415021.076998695) / 29.530588853 + 0.5);
        $arc  = (int) floor($this->getSunLongitude($this->getNewMoonDay($k + 1, $tz), $tz) / 30);
        $last = 0;
        $i    = 1;

        do {
            $last = $arc;
            $i++;
            $arc  = (int) floor($this->getSunLongitude($this->getNewMoonDay($k + $i, $tz), $tz) / 30);
        } while ($arc !== $last && $i < 14);

        return $i - 1;
    }

    /**
     * Core Solar→Lunar conversion (Hồ Ngọc Đức algorithm).
     *
     * @return array{0: int, 1: int, 2: int, 3: bool}  [lunarDay, lunarMonth, lunarYear, isLeapMonth]
     */
    private function convertSolar2Lunar(int $dd, int $mm, int $yyyy, float $tz): array
    {
        $dayNumber  = $this->jdFromDate($dd, $mm, $yyyy);
        $k          = (int) floor(($dayNumber - 2415021.076998695) / 29.530588853);
        $monthStart = $this->getNewMoonDay($k + 1, $tz);

        if ($monthStart > $dayNumber) {
            $monthStart = $this->getNewMoonDay($k, $tz);
        }

        $a11 = $this->getLunarMonth11($yyyy, $tz);
        $b11 = $a11;

        if ($a11 >= $monthStart) {
            $lunarYear = $yyyy;
            $a11       = $this->getLunarMonth11($yyyy - 1, $tz);
        } else {
            $lunarYear = $yyyy + 1;
            $b11       = $this->getLunarMonth11($yyyy + 1, $tz);
        }

        $lunarDay   = $dayNumber - $monthStart + 1;
        $diff       = (int) floor(($monthStart - $a11) / 29);
        $lunarLeap  = false;
        $lunarMonth = $diff + 11;

        if (($b11 - $a11) > 365) {
            $leapOffset = $this->getLeapMonthOffset($a11, $tz);
            if ($diff >= $leapOffset) {
                $lunarMonth = $diff + 10;
                if ($diff === $leapOffset) {
                    $lunarLeap = true;
                }
            }
        }

        if ($lunarMonth > 12) {
            $lunarMonth -= 12;
        }

        if ($lunarMonth >= 11 && $diff < 4) {
            $lunarYear--;
        }

        return [(int) $lunarDay, (int) $lunarMonth, (int) $lunarYear, $lunarLeap];
    }
}
