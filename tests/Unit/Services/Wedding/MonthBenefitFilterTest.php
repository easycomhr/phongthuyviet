<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Wedding;

use App\Services\LunarCalendarService;
use App\Services\Wedding\Filters\MonthBenefitFilter;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class MonthBenefitFilterTest extends TestCase
{
    private MonthBenefitFilter $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new MonthBenefitFilter(new LunarCalendarService());
    }

    /** @test */
    public function it_switches_tiet_khi_month_at_lap_xuan_boundary(): void
    {
        $lapXuan = $this->filter->findSolarTermDate(2026, 'Lập Xuân');
        $before = $lapXuan->subDay();

        $monthBefore = $this->filter->resolveTietKhiMonth(CarbonImmutable::create(
            $before->year,
            $before->month,
            $before->day,
            0,
            0,
            0,
            'Asia/Ho_Chi_Minh',
        ));
        $monthAt = $this->filter->resolveTietKhiMonth(CarbonImmutable::create(
            $lapXuan->year,
            $lapXuan->month,
            $lapXuan->day,
            0,
            0,
            0,
            'Asia/Ho_Chi_Minh',
        ));

        $this->assertSame(12, $monthBefore, 'Ngày trước Lập Xuân phải thuộc tháng tiết khí 12.');
        $this->assertSame(1, $monthAt, 'Ngày Lập Xuân phải thuộc tháng tiết khí 1.');
    }
}

