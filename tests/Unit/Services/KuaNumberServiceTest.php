<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\KuaNumberService;
use App\Services\LunarCalendarService;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class KuaNumberServiceTest extends TestCase
{
    private KuaNumberService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KuaNumberService(new LunarCalendarService());
    }

    /** @test */
    public function it_uses_previous_feng_shui_year_when_birth_is_on_lap_xuan_cutoff_day(): void
    {
        $result = $this->service->analyze(
            'male',
            CarbonImmutable::create(1993, 2, 4, 0, 0, 0, 'Asia/Ho_Chi_Minh'),
        );

        $this->assertSame(1992, $result['lunarYearForFengShui']);
    }

    /** @test */
    public function it_applies_different_formula_for_1999_and_2000_for_male(): void
    {
        $kua1999 = $this->service->calculateKuaNumber('male', 1999);
        $kua2000 = $this->service->calculateKuaNumber('male', 2000);

        $this->assertSame(1, $kua1999);
        $this->assertSame(9, $kua2000);
    }

    /** @test */
    public function kua_five_is_mapped_to_gender_specific_house(): void
    {
        $male = $this->service->calculateKuaNumber('male', 1986);     // N=5 => 10-5=5 -> 2
        $female = $this->service->calculateKuaNumber('female', 1999); // N=9 => 14 => 5 -> Cấn(8)

        $this->assertSame(2, $male);
        $this->assertSame(8, $female);
    }
}
