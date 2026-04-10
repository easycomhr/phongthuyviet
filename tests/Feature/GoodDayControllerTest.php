<?php

declare(strict_types=1);

namespace Tests\Feature;

use Database\Seeders\EventCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoodDayControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(EventCategorySeeder::class);
    }

    /** @test */
    public function index_route_returns_200(): void
    {
        $response = $this->get('/tra-cuu-ngay-tot?category_slug=cuoi-hoi&month_year=2025-01');
        $response->assertStatus(200);
    }

    /** @test */
    public function index_route_is_named_gooddays_index(): void
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Route::has('gooddays.index'),
            'Route name "gooddays.index" must be registered'
        );
    }

    /** @test */
    public function index_renders_inertia_gooddays_index_component(): void
    {
        $response = $this->get('/tra-cuu-ngay-tot?category_slug=cuoi-hoi&month_year=2025-01');
        $response->assertInertia(fn ($page) => $page->component('GoodDays/Index'));
    }

    /** @test */
    public function index_passes_required_props(): void
    {
        $response = $this->get('/tra-cuu-ngay-tot?category_slug=cuoi-hoi&month_year=2025-01');

        $response->assertInertia(fn ($page) => $page
            ->has('days')
            ->has('category')
            ->has('monthYear')
            ->has('categories')
        );
    }

    /** @test */
    public function days_array_has_31_entries_for_january(): void
    {
        $response = $this->get('/tra-cuu-ngay-tot?category_slug=cuoi-hoi&month_year=2025-01');

        $response->assertInertia(fn ($page) => $page
            ->has('days', 31)
        );
    }

    /** @test */
    public function days_array_has_28_entries_for_february_2025(): void
    {
        $response = $this->get('/tra-cuu-ngay-tot?category_slug=cuoi-hoi&month_year=2025-02');

        $response->assertInertia(fn ($page) => $page
            ->has('days', 28)
        );
    }

    /** @test */
    public function missing_category_slug_fails_validation(): void
    {
        $response = $this->get('/tra-cuu-ngay-tot?month_year=2025-01');
        $response->assertSessionHasErrors('category_slug');
    }

    /** @test */
    public function invalid_category_slug_fails_validation(): void
    {
        $response = $this->get('/tra-cuu-ngay-tot?category_slug=not-exist&month_year=2025-01');
        $response->assertSessionHasErrors('category_slug');
    }

    /** @test */
    public function missing_month_year_fails_validation(): void
    {
        $response = $this->get('/tra-cuu-ngay-tot?category_slug=cuoi-hoi');
        $response->assertSessionHasErrors('month_year');
    }

    /** @test */
    public function invalid_month_year_format_fails_validation(): void
    {
        $response = $this->get('/tra-cuu-ngay-tot?category_slug=cuoi-hoi&month_year=01-2025');
        $response->assertSessionHasErrors('month_year');
    }

    /** @test */
    public function category_prop_contains_correct_slug(): void
    {
        $response = $this->get('/tra-cuu-ngay-tot?category_slug=cuoi-hoi&month_year=2025-01');

        $response->assertInertia(fn ($page) => $page
            ->where('category.slug', 'cuoi-hoi')
        );
    }

    /** @test */
    public function month_year_prop_matches_request(): void
    {
        $response = $this->get('/tra-cuu-ngay-tot?category_slug=cuoi-hoi&month_year=2025-01');

        $response->assertInertia(fn ($page) => $page
            ->where('monthYear', '2025-01')
        );
    }
}
