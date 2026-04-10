<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Naming;

use App\Contracts\AstrologyEngineInterface;
use App\Services\Naming\MissingElementCalculator;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class MissingElementCalculatorTest extends TestCase
{
    /** @test */
    public function balanced_bazi_uses_fallback_and_still_returns_target_elements(): void
    {
        $adapter = new class implements AstrologyEngineInterface {
            public function calculateChart(Carbon $birthDate, string $gender): array
            {
                return [];
            }

            public function getLunarDate(Carbon $solarDate): array
            {
                return ['lunar_day' => 1, 'lunar_month' => 1, 'lunar_year' => 2026, 'is_leap_month' => false];
            }

            public function calculateBaziProfile(Carbon $birthDate): array
            {
                return [
                    'pillars' => [],
                    'element_count' => ['kim' => 2, 'moc' => 2, 'thuy' => 2, 'hoa' => 2, 'tho' => 2],
                    'dominant_element' => 'kim',
                    'missing_elements' => [],
                ];
            }
        };

        $service = new MissingElementCalculator($adapter);
        $result = $service->analyze(Carbon::create(2026, 10, 20, 9, 30, 0, 'Asia/Ho_Chi_Minh'), ['moc', 'hoa']);

        $this->assertTrue($result['fallback_used']);
        $this->assertNotEmpty($result['target_elements']);
        $this->assertContains($result['target_elements'][0], ['moc', 'hoa']);
    }
}

