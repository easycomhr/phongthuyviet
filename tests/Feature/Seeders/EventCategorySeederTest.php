<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use Database\Seeders\EventCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCategorySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_inserts_exactly_10_event_categories(): void
    {
        $this->seed(EventCategorySeeder::class);

        $this->assertDatabaseCount('event_categories', 10);
    }

    public function test_seeder_marks_popular_categories(): void
    {
        $this->seed(EventCategorySeeder::class);

        $popularCount = \App\Models\EventCategory::popular()->count();
        $this->assertGreaterThanOrEqual(1, $popularCount);
    }

    public function test_seeder_all_slugs_are_unique(): void
    {
        $this->seed(EventCategorySeeder::class);

        $totalSlugs = \App\Models\EventCategory::count();
        $uniqueSlugs = \App\Models\EventCategory::distinct()->count('slug');
        $this->assertEquals($totalSlugs, $uniqueSlugs);
    }

    public function test_seeder_contains_required_categories(): void
    {
        $this->seed(EventCategorySeeder::class);

        $this->assertDatabaseHas('event_categories', ['slug' => 'cuoi-hoi']);
        $this->assertDatabaseHas('event_categories', ['slug' => 'khoi-cong']);
        $this->assertDatabaseHas('event_categories', ['slug' => 'khai-truong']);
        $this->assertDatabaseHas('event_categories', ['slug' => 'nhap-trach']);
    }
}
