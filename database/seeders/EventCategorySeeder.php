<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EventCategory;
use Illuminate\Database\Seeder;

class EventCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Cưới hỏi',              'slug' => 'cuoi-hoi',        'icon' => 'icon-wedding',    'is_popular' => true],
            ['name' => 'Khởi công',              'slug' => 'khoi-cong',       'icon' => 'icon-construct',  'is_popular' => true],
            ['name' => 'Khai trương',            'slug' => 'khai-truong',     'icon' => 'icon-opening',    'is_popular' => true],
            ['name' => 'Nhập trạch',             'slug' => 'nhap-trach',      'icon' => 'icon-house',      'is_popular' => true],
            ['name' => 'Xuất hành',              'slug' => 'xuat-hanh',       'icon' => 'icon-travel',     'is_popular' => true],
            ['name' => 'Cúng ông Công ông Táo',  'slug' => 'cung-tao-quan',   'icon' => 'icon-pray',       'is_popular' => true],
            ['name' => 'Động thổ',               'slug' => 'dong-tho',        'icon' => 'icon-ground',     'is_popular' => false],
            ['name' => 'An táng',                'slug' => 'an-tang',         'icon' => 'icon-funeral',    'is_popular' => false],
            ['name' => 'Giỗ chạp',               'slug' => 'gio-chap',        'icon' => 'icon-ancestor',   'is_popular' => false],
            ['name' => 'Lễ cúng nhà mới',        'slug' => 'cung-nha-moi',    'icon' => 'icon-new-house',  'is_popular' => false],
        ];

        foreach ($categories as $category) {
            EventCategory::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
