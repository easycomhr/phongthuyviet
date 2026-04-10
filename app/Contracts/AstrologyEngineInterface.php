<?php

declare(strict_types=1);

namespace App\Contracts;

use Carbon\Carbon;

interface AstrologyEngineInterface
{
    /**
     * @param 'male'|'female' $gender
     * @return array<string, mixed>
     */
    public function calculateChart(Carbon $birthDate, string $gender): array;

    /**
     * @return array{lunar_day: int, lunar_month: int, lunar_year: int, is_leap_month: bool}
     */
    public function getLunarDate(Carbon $solarDate): array;

    /**
     * @return array{
     *   pillars: array<string, array{can: string, chi: string, can_element: string, chi_element: string}>,
     *   element_count: array{kim:int,moc:int,thuy:int,hoa:int,tho:int},
     *   dominant_element: string,
     *   missing_elements: list<string>
     * }
     */
    public function calculateBaziProfile(Carbon $birthDate): array;
}
