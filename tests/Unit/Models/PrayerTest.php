<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Prayer;
use App\Models\PrayerCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrayerTest extends TestCase
{
    use RefreshDatabase;

    private PrayerCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = PrayerCategory::create(['name' => 'Văn khấn gia tiên', 'slug' => 'van-khan-gia-tien']);
    }

    public function test_prayers_table_has_correct_columns(): void
    {
        $columns = \Schema::getColumnListing('prayers');

        $this->assertContains('id', $columns);
        $this->assertContains('category_id', $columns);
        $this->assertContains('title', $columns);
        $this->assertContains('slug', $columns);
        $this->assertContains('content', $columns);
        $this->assertContains('created_at', $columns);
        $this->assertContains('updated_at', $columns);
    }

    public function test_prayer_belongs_to_prayer_category(): void
    {
        $prayer = Prayer::create([
            'category_id' => $this->category->id,
            'title'       => 'Văn khấn ngày mùng 1',
            'slug'        => 'van-khan-mung-1',
            'content'     => 'Nội dung bài khấn...',
        ]);

        $this->assertInstanceOf(PrayerCategory::class, $prayer->prayerCategory);
        $this->assertEquals($this->category->id, $prayer->prayerCategory->id);
    }

    public function test_prayer_category_has_many_prayers(): void
    {
        Prayer::create([
            'category_id' => $this->category->id,
            'title'       => 'Bài khấn 1',
            'slug'        => 'bai-khan-1',
            'content'     => 'Nội dung 1',
        ]);
        Prayer::create([
            'category_id' => $this->category->id,
            'title'       => 'Bài khấn 2',
            'slug'        => 'bai-khan-2',
            'content'     => 'Nội dung 2',
        ]);

        $this->assertCount(2, $this->category->prayers);
    }

    public function test_prayer_slug_is_unique(): void
    {
        Prayer::create([
            'category_id' => $this->category->id,
            'title'       => 'Bài khấn',
            'slug'        => 'bai-khan-unique',
            'content'     => 'Nội dung',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Prayer::create([
            'category_id' => $this->category->id,
            'title'       => 'Bài khấn khác',
            'slug'        => 'bai-khan-unique',
            'content'     => 'Nội dung khác',
        ]);
    }

    public function test_deleting_category_cascades_to_prayers(): void
    {
        Prayer::create([
            'category_id' => $this->category->id,
            'title'       => 'Bài khấn cascade',
            'slug'        => 'bai-khan-cascade',
            'content'     => 'Nội dung',
        ]);

        $this->category->delete();

        $this->assertDatabaseMissing('prayers', ['slug' => 'bai-khan-cascade']);
    }
}
