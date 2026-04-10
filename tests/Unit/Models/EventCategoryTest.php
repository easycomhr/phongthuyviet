<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\EventCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_category_table_has_correct_columns(): void
    {
        $columns = \Schema::getColumnListing('event_categories');

        $this->assertContains('id', $columns);
        $this->assertContains('name', $columns);
        $this->assertContains('slug', $columns);
        $this->assertContains('icon', $columns);
        $this->assertContains('is_popular', $columns);
        $this->assertContains('created_at', $columns);
        $this->assertContains('updated_at', $columns);
    }

    public function test_slug_is_unique(): void
    {
        EventCategory::create(['name' => 'Cưới hỏi', 'slug' => 'cuoi-hoi']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        EventCategory::create(['name' => 'Cưới hỏi 2', 'slug' => 'cuoi-hoi']);
    }

    public function test_is_popular_defaults_to_false(): void
    {
        $category = EventCategory::create(['name' => 'Test', 'slug' => 'test']);
        $this->assertFalse($category->is_popular);
    }

    public function test_scope_popular_returns_only_popular_items(): void
    {
        EventCategory::create(['name' => 'Cưới hỏi', 'slug' => 'cuoi-hoi', 'is_popular' => true]);
        EventCategory::create(['name' => 'Động thổ', 'slug' => 'dong-tho', 'is_popular' => false]);

        $popular = EventCategory::popular()->get();

        $this->assertCount(1, $popular);
        $this->assertEquals('cuoi-hoi', $popular->first()->slug);
    }

    public function test_icon_is_nullable(): void
    {
        $category = EventCategory::create(['name' => 'Test', 'slug' => 'test-icon']);
        $this->assertNull($category->icon);
    }
}
