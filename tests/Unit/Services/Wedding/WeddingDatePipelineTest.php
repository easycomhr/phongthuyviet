<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Wedding;

use App\DTO\Wedding\PartnerDTO;
use App\DTO\Wedding\TargetYearMonthDTO;
use App\Services\Astrology\TuViLibraryAdapter;
use App\Services\FengShuiDailyService;
use App\Services\LunarCalendarService;
use App\Services\Wedding\Filters\DayAuspiciousFilter;
use App\Services\Wedding\Filters\KimLauFilter;
use App\Services\Wedding\Filters\MonthBenefitFilter;
use App\Services\Wedding\Filters\ZodiacClashFilter;
use App\Services\Wedding\WeddingDatePipeline;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class WeddingDatePipelineTest extends TestCase
{
    /** @test */
    public function pipeline_does_not_duplicate_dates_in_lunar_leap_year_context(): void
    {
        $lunar = new LunarCalendarService();
        $adapter = new TuViLibraryAdapter($lunar);
        $pipeline = new WeddingDatePipeline(
            new KimLauFilter($adapter),
            new MonthBenefitFilter($lunar),
            new DayAuspiciousFilter($lunar, new FengShuiDailyService($lunar)),
            new ZodiacClashFilter($lunar),
        );

        $bride = new PartnerDTO('Lan', 'female', CarbonImmutable::create(1998, 9, 2, 0, 0, 0, 'Asia/Ho_Chi_Minh'));
        $groom = new PartnerDTO('Minh', 'male', CarbonImmutable::create(1996, 7, 12, 0, 0, 0, 'Asia/Ho_Chi_Minh'));
        $target = new TargetYearMonthDTO(2025, 8);

        $result = $pipeline->run($bride, $groom, $target, 'wedding');
        $dates = array_map(static fn (array $d): string => $d['date'], $result['dates']);

        $this->assertSameSize(array_unique($dates), $dates, 'Danh sách ngày không được trùng lặp.');
    }
}
