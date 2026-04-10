<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\EventCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_returns_200(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_home_page_passes_categories_prop_to_inertia(): void
    {
        EventCategory::create(['name' => 'Cưới hỏi', 'slug' => 'cuoi-hoi', 'is_popular' => true]);
        EventCategory::create(['name' => 'Động thổ', 'slug' => 'dong-tho', 'is_popular' => false]);

        $response = $this->get('/');

        $response->assertInertia(fn ($page) =>
            $page->component('Home')
                 ->has('categories', 1)
                 ->where('categories.0.slug', 'cuoi-hoi')
        );
    }

    public function test_home_page_only_passes_popular_categories(): void
    {
        EventCategory::create(['name' => 'Cưới hỏi',  'slug' => 'cuoi-hoi',  'is_popular' => true]);
        EventCategory::create(['name' => 'Khởi công', 'slug' => 'khoi-cong', 'is_popular' => true]);
        EventCategory::create(['name' => 'Động thổ',  'slug' => 'dong-tho',  'is_popular' => false]);

        $response = $this->get('/');

        $response->assertInertia(fn ($page) =>
            $page->component('Home')->has('categories', 2)
        );
    }
}
