<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function today_route_returns_200(): void
    {
        $response = $this->get('/lich-hom-nay');

        $response->assertStatus(200);
    }

    /** @test */
    public function today_route_is_named_calendar_today(): void
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Route::has('calendar.today'),
            'Route name "calendar.today" must be registered'
        );
    }

    /** @test */
    public function today_renders_inertia_calendar_today_component(): void
    {
        $response = $this->get('/lich-hom-nay');

        $response->assertInertia(fn ($page) => $page->component('Calendar/Today'));
    }

    /** @test */
    public function today_passes_required_props(): void
    {
        $response = $this->get('/lich-hom-nay');

        $response->assertInertia(fn ($page) => $page
            ->has('daily')
            ->has('daily.solar')
            ->has('daily.lunar')
            ->has('daily.canChi')
            ->has('daily.luckyHours')
            ->has('daily.conflictZodiacs')
            ->has('daily.directions')
            ->has('prevDate')
            ->has('nextDate')
        );
    }

    /** @test */
    public function it_accepts_a_date_query_param(): void
    {
        $response = $this->get('/lich-hom-nay?date=2025-01-29');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('daily.solar.day',   29)
            ->where('daily.solar.month', 1)
            ->where('daily.solar.year',  2025)
        );
    }

    /** @test */
    public function it_returns_lunar_new_year_data_for_tet_2025(): void
    {
        $response = $this->get('/lich-hom-nay?date=2025-01-29');

        $response->assertInertia(fn ($page) => $page
            ->where('daily.lunar.day',   1)
            ->where('daily.lunar.month', 1)
            ->where('daily.lunar.year',  2025)
        );
    }

    /** @test */
    public function prevDate_and_nextDate_are_adjacent_to_requested_date(): void
    {
        $response = $this->get('/lich-hom-nay?date=2025-01-29');

        $response->assertInertia(fn ($page) => $page
            ->where('prevDate', '2025-01-28')
            ->where('nextDate', '2025-01-30')
        );
    }

    /** @test */
    public function invalid_date_format_redirects_with_validation_error(): void
    {
        $response = $this->get('/lich-hom-nay?date=not-a-date');

        $response->assertSessionHasErrors('date');
    }
}
