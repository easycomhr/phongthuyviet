<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\EventCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchDayRequestTest extends TestCase
{
    use RefreshDatabase;

    private function seedCategory(): void
    {
        EventCategory::create(['name' => 'Cưới hỏi', 'slug' => 'cuoi-hoi', 'is_popular' => true]);
    }

    public function test_search_returns_redirect_with_valid_params(): void
    {
        $this->seedCategory();

        $response = $this->get('/tra-cuu?category_slug=cuoi-hoi&date=2026-04-15');

        $response->assertStatus(200);
    }

    public function test_search_requires_category_slug(): void
    {
        $response = $this->get('/tra-cuu?date=2026-04-15');

        $response->assertSessionHasErrors('category_slug');
    }

    public function test_search_requires_date(): void
    {
        $this->seedCategory();

        $response = $this->get('/tra-cuu?category_slug=cuoi-hoi');

        $response->assertSessionHasErrors('date');
    }

    public function test_search_rejects_invalid_date_format(): void
    {
        $this->seedCategory();

        $response = $this->get('/tra-cuu?category_slug=cuoi-hoi&date=15-04-2026');

        $response->assertSessionHasErrors('date');
    }

    public function test_search_rejects_nonexistent_category_slug(): void
    {
        $response = $this->get('/tra-cuu?category_slug=khong-ton-tai&date=2026-04-15');

        $response->assertSessionHasErrors('category_slug');
    }

    public function test_search_route_is_named_search_index(): void
    {
        $this->assertEquals('/tra-cuu', route('search.index', [], false));
    }
}
