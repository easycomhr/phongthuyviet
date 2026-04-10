<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Astrology;

use App\Services\Astrology\TuViLibraryAdapter;
use App\Services\LunarCalendarService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class TuViLibraryAdapterTest extends TestCase
{
    private TuViLibraryAdapter $adapter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adapter = new TuViLibraryAdapter(new LunarCalendarService());
    }

    /** @test */
    public function it_returns_normalized_chart_payload(): void
    {
        $chart = $this->adapter->calculateChart(Carbon::create(1993, 4, 9, 0, 0, 0, 'Asia/Ho_Chi_Minh'), 'male');

        $this->assertArrayHasKey('profile', $chart);
        $this->assertArrayHasKey('palaces', $chart);
        $this->assertCount(12, $chart['palaces']);
        $this->assertArrayHasKey('stars', $chart['palaces'][0]);
    }

    /** @test */
    public function it_returns_lunar_date_shape(): void
    {
        $lunar = $this->adapter->getLunarDate(Carbon::create(2026, 4, 10, 0, 0, 0, 'Asia/Ho_Chi_Minh'));

        $this->assertArrayHasKey('lunar_day', $lunar);
        $this->assertArrayHasKey('lunar_month', $lunar);
        $this->assertArrayHasKey('lunar_year', $lunar);
        $this->assertArrayHasKey('is_leap_month', $lunar);
    }
}

