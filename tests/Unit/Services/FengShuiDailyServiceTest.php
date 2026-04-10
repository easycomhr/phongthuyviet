<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\FengShuiDailyService;
use App\Services\LunarCalendarService;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class FengShuiDailyServiceTest extends TestCase
{
    private FengShuiDailyService $service;

    protected function setUp(): void
    {
        $this->service = new FengShuiDailyService(new LunarCalendarService());
    }

    // ── Output contract ───────────────────────────────────────────────────────

    /** @test */
    public function getDaily_returns_all_required_top_level_keys(): void
    {
        $date   = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->getDaily($date);

        foreach (['solar', 'lunar', 'canChi', 'solarTerm', 'luckyHours', 'conflictZodiacs', 'directions'] as $key) {
            $this->assertArrayHasKey($key, $result, "Missing key: $key");
        }
    }

    /** @test */
    public function solar_block_has_correct_shape(): void
    {
        $date   = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->getDaily($date);

        $solar = $result['solar'];
        $this->assertSame(29,    $solar['day']);
        $this->assertSame(1,     $solar['month']);
        $this->assertSame(2025,  $solar['year']);
        $this->assertArrayHasKey('weekday', $solar);
    }

    /** @test */
    public function lunar_block_has_correct_tet_2025_values(): void
    {
        $date   = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->getDaily($date);

        $lunar = $result['lunar'];
        $this->assertSame(1,    $lunar['day']);
        $this->assertSame(1,    $lunar['month']);
        $this->assertSame(2025, $lunar['year']);
        $this->assertFalse($lunar['isLeapMonth']);
    }

    /** @test */
    public function canChi_block_has_all_six_keys(): void
    {
        $date   = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->getDaily($date);

        foreach (['dayCan', 'dayChi', 'monthCan', 'monthChi', 'yearCan', 'yearChi'] as $key) {
            $this->assertArrayHasKey($key, $result['canChi']);
        }
    }

    // ── solarTerm ─────────────────────────────────────────────────────────────

    /** @test */
    public function solarTerm_is_null_for_non_term_day(): void
    {
        $date   = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->getDaily($date);

        $this->assertNull($result['solarTerm']);
    }

    /** @test */
    public function solarTerm_is_set_on_lap_xuan(): void
    {
        $date   = CarbonImmutable::create(2025, 2, 4, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->getDaily($date);

        $this->assertSame('Lập Xuân', $result['solarTerm']);
    }

    // ── luckyHours ────────────────────────────────────────────────────────────

    /** @test */
    public function luckyHours_has_twelve_entries(): void
    {
        $date   = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->getDaily($date);

        $this->assertCount(12, $result['luckyHours']);
    }

    /** @test */
    public function luckyHours_each_entry_has_name_and_isLucky(): void
    {
        $date  = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $hours = $this->service->getDaily($date)['luckyHours'];

        foreach ($hours as $hour) {
            $this->assertArrayHasKey('name', $hour);
            $this->assertArrayHasKey('isLucky', $hour);
            $this->assertIsBool($hour['isLucky']);
        }
    }

    /** @test */
    public function luckyHours_has_exactly_six_lucky_and_six_unlucky(): void
    {
        $date  = CarbonImmutable::create(2025, 3, 10, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $hours = $this->service->getDaily($date)['luckyHours'];

        $lucky   = array_filter($hours, fn($h) => $h['isLucky']);
        $unlucky = array_filter($hours, fn($h) => ! $h['isLucky']);

        $this->assertCount(6, $lucky,   '6 Hoàng Đạo hours expected');
        $this->assertCount(6, $unlucky, '6 Hắc Đạo hours expected');
    }

    // ── conflictZodiacs ───────────────────────────────────────────────────────

    /** @test */
    public function conflictZodiacs_is_non_empty_array(): void
    {
        $date   = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->getDaily($date);

        $this->assertIsArray($result['conflictZodiacs']);
        $this->assertNotEmpty($result['conflictZodiacs']);
    }

    /** @test */
    public function conflictZodiacs_uses_valid_chi_values(): void
    {
        $validChi = ['Tý', 'Sửu', 'Dần', 'Mão', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tuất', 'Hợi'];

        $date    = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $zodiacs = $this->service->getDaily($date)['conflictZodiacs'];

        foreach ($zodiacs as $zodiac) {
            $this->assertContains($zodiac, $validChi, "Invalid zodiac: $zodiac");
        }
    }

    // ── directions ────────────────────────────────────────────────────────────

    /** @test */
    public function directions_has_happiness_and_wealth_keys(): void
    {
        $date       = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $directions = $this->service->getDaily($date)['directions'];

        $this->assertArrayHasKey('happiness', $directions);
        $this->assertArrayHasKey('wealth',    $directions);
        $this->assertNotEmpty($directions['happiness']);
        $this->assertNotEmpty($directions['wealth']);
    }

    /** @test */
    public function directions_are_valid_compass_directions(): void
    {
        $valid = ['Bắc', 'Nam', 'Đông', 'Tây', 'Đông Bắc', 'Đông Nam', 'Tây Bắc', 'Tây Nam'];

        $date       = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $directions = $this->service->getDaily($date)['directions'];

        $this->assertContains($directions['happiness'], $valid);
        $this->assertContains($directions['wealth'],    $valid);
    }
}
