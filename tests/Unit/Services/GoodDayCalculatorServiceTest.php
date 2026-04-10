<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\FengShuiDailyService;
use App\Services\GoodDayCalculatorService;
use App\Services\LunarCalendarService;
use PHPUnit\Framework\TestCase;

class GoodDayCalculatorServiceTest extends TestCase
{
    private GoodDayCalculatorService $service;

    protected function setUp(): void
    {
        $lunar        = new LunarCalendarService();
        $fengShui     = new FengShuiDailyService($lunar);
        $this->service = new GoodDayCalculatorService($lunar, $fengShui);
    }

    // ── getMonthDays: output shape ─────────────────────────────────────────

    /** @test */
    public function it_returns_correct_day_count_for_january_2025(): void
    {
        $days = $this->service->getMonthDays('cuoi-hoi', 2025, 1);
        $this->assertCount(31, $days);
    }

    /** @test */
    public function it_returns_correct_day_count_for_february_2024_leap(): void
    {
        $days = $this->service->getMonthDays('cuoi-hoi', 2024, 2);
        $this->assertCount(29, $days);
    }

    /** @test */
    public function each_day_has_required_keys(): void
    {
        $days = $this->service->getMonthDays('cuoi-hoi', 2025, 1);
        $day  = $days[0];

        foreach (['date', 'solar', 'lunar', 'canChi', 'truc', 'xiuSao',
                  'score', 'isGood', 'isTamNuong', 'isNguyetKy', 'luckyHours'] as $key) {
            $this->assertArrayHasKey($key, $day, "Key '$key' missing");
        }
    }

    /** @test */
    public function first_day_of_month_has_correct_solar_date(): void
    {
        $days = $this->service->getMonthDays('cuoi-hoi', 2025, 1);
        $this->assertSame('2025-01-01', $days[0]['date']);
        $this->assertSame(1,  $days[0]['solar']['day']);
        $this->assertSame(1,  $days[0]['solar']['month']);
        $this->assertSame(2025, $days[0]['solar']['year']);
    }

    /** @test */
    public function last_day_of_january_is_31(): void
    {
        $days = $this->service->getMonthDays('cuoi-hoi', 2025, 1);
        $last = $days[30];
        $this->assertSame('2025-01-31', $last['date']);
        $this->assertSame(31, $last['solar']['day']);
    }

    // ── Tam Nương / Nguyệt Kỵ flags ──────────────────────────────────────

    /** @test */
    public function tam_nuong_days_are_marked_not_good(): void
    {
        // Tháng 1/2025: Mùng 1 = Jan 29 (solar). Âm ngày 3 = Jan 31.
        // Cần tìm ngày Tam Nương trong tháng 1 dương (Jan 1–31).
        $days = $this->service->getMonthDays('cuoi-hoi', 2025, 1);

        $tamNuongDays = array_filter($days, fn ($d) => $d['isTamNuong']);
        $this->assertNotEmpty($tamNuongDays, 'Phải có ít nhất 1 ngày Tam Nương trong tháng');

        foreach ($tamNuongDays as $day) {
            $this->assertFalse($day['isGood'], "Ngày Tam Nương {$day['date']} không được là ngày tốt");
        }
    }

    /** @test */
    public function nguyet_ky_days_have_reduced_score(): void
    {
        $days = $this->service->getMonthDays('cuoi-hoi', 2025, 1);

        $nguyetKyDays = array_filter($days, fn ($d) => $d['isNguyetKy']);
        $this->assertNotEmpty($nguyetKyDays, 'Phải có ít nhất 1 ngày Nguyệt Kỵ trong tháng');

        $normalScores = array_map(fn ($d) => $d['score'],
            array_filter($days, fn ($d) => !$d['isNguyetKy'] && !$d['isTamNuong'])
        );
        $avgNormal = array_sum($normalScores) / count($normalScores);

        $avgNguyetKy = array_sum(array_map(fn ($d) => $d['score'], $nguyetKyDays))
            / count($nguyetKyDays);

        $this->assertLessThan($avgNormal, $avgNguyetKy, 'Nguyệt Kỵ nên có score thấp hơn bình thường');
    }

    // ── Scoring ───────────────────────────────────────────────────────────

    /** @test */
    public function score_is_integer_between_0_and_100(): void
    {
        $days = $this->service->getMonthDays('cuoi-hoi', 2025, 1);
        foreach ($days as $day) {
            $this->assertIsInt($day['score']);
            $this->assertGreaterThanOrEqual(0,   $day['score']);
            $this->assertLessThanOrEqual(100, $day['score']);
        }
    }

    /** @test */
    public function is_good_is_bool(): void
    {
        $days = $this->service->getMonthDays('cuoi-hoi', 2025, 1);
        foreach ($days as $day) {
            $this->assertIsBool($day['isGood']);
        }
    }

    /** @test */
    public function month_has_both_good_and_bad_days(): void
    {
        $days = $this->service->getMonthDays('cuoi-hoi', 2025, 1);
        $good = array_filter($days, fn ($d) => $d['isGood']);
        $bad  = array_filter($days, fn ($d) => !$d['isGood']);
        $this->assertNotEmpty($good, 'Cần có ít nhất 1 ngày tốt trong tháng');
        $this->assertNotEmpty($bad,  'Cần có ít nhất 1 ngày xấu trong tháng');
    }

    // ── Lucky hours shape ─────────────────────────────────────────────────

    /** @test */
    public function lucky_hours_has_12_slots(): void
    {
        $days = $this->service->getMonthDays('cuoi-hoi', 2025, 1);
        foreach ($days as $day) {
            $this->assertCount(12, $day['luckyHours'], "Ngày {$day['date']} phải có đúng 12 giờ");
        }
    }
}
