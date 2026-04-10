<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PrayerCategory;
use Illuminate\Database\Seeder;

class PrayerCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Văn khấn gia tiên',              'slug' => 'gia-tien'],
            ['name' => 'Văn khấn mùng 1 - ngày rằm',     'slug' => 'mung-1-ngay-ram'],
            ['name' => 'Văn khấn nhập trạch',            'slug' => 'nhap-trach'],
            ['name' => 'Văn khấn khai trương',           'slug' => 'khai-truong'],
            ['name' => 'Văn khấn động thổ',              'slug' => 'dong-tho'],
            ['name' => 'Văn khấn cúng ông Công ông Táo', 'slug' => 'ong-cong-ong-tao'],
            ['name' => 'Văn khấn giao thừa',             'slug' => 'giao-thua'],
            ['name' => 'Văn khấn tất niên',              'slug' => 'tat-nien'],
            ['name' => 'Văn khấn lễ chùa',               'slug' => 'le-chua'],
            ['name' => 'Văn khấn thần tài - thổ địa',    'slug' => 'than-tai-tho-dia'],
            ['name' => 'Văn khấn thôi nôi',              'slug' => 'thoi-noi'],
            ['name' => 'Văn khấn đầy tháng',             'slug' => 'day-thang'],
            ['name' => 'Văn khấn cưới hỏi',              'slug' => 'cuoi-hoi'],
            ['name' => 'Văn khấn giỗ chạp',              'slug' => 'gio-chap'],
            ['name' => 'Văn khấn xuất hành',             'slug' => 'xuat-hanh'],
            ['name' => 'Văn khấn hiếu lễ - tang nghi',   'slug' => 'hieu-le-tang-nghi'],
            ['name' => 'Văn khấn tạ mộ - thanh minh',    'slug' => 'ta-mo-thanh-minh'],
        ];

        foreach ($categories as $category) {
            PrayerCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category,
            );
        }
    }
}
