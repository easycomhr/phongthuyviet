<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\VietnameseNameDictionary;
use Illuminate\Database\Seeder;

class VietnameseNamesDictionarySeeder extends Seeder
{
    public function run(): void
    {
        /** @var list<array{name:string,element:string,gender:string,meaning:string}> $rows */
        $rows = config('naming_library.dictionary', []);

        foreach ($rows as $row) {
            VietnameseNameDictionary::updateOrCreate(
                ['name' => $row['name'], 'gender' => $row['gender']],
                $row,
            );
        }
    }
}
