<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Naming;

use App\Models\VietnameseNameDictionary;
use App\Services\Naming\NameGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NameGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_filters_out_taboo_name_matching_parents_given_names(): void
    {
        VietnameseNameDictionary::create([
            'name' => 'Anh',
            'element' => 'moc',
            'gender' => 'unisex',
            'meaning' => 'Tên thử phạm húy.',
        ]);
        VietnameseNameDictionary::create([
            'name' => 'Bách',
            'element' => 'moc',
            'gender' => 'male',
            'meaning' => 'Tên phù hợp.',
        ]);

        $service = new NameGeneratorService();
        $result = $service->generate(
            'Nguyễn Văn Anh',
            'Lê Thị Lan',
            'male',
            true,
            ['moc'],
            ['moc', 'hoa'],
        );

        $baseNames = array_map(static fn (array $s): string => $s['base_name'], $result['suggestions']);
        $this->assertNotContains('Anh', $baseNames);
        $this->assertContains('Bách', $baseNames);
    }
}

