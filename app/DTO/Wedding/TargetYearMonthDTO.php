<?php

declare(strict_types=1);

namespace App\DTO\Wedding;

use Carbon\CarbonImmutable;

class TargetYearMonthDTO
{
    public function __construct(
        public readonly int $year,
        public readonly int $month,
    ) {}

    public function startDate(): CarbonImmutable
    {
        return CarbonImmutable::create($this->year, $this->month, 1, 0, 0, 0, 'Asia/Ho_Chi_Minh');
    }

    public function endDate(): CarbonImmutable
    {
        return $this->startDate()->endOfMonth();
    }

    public function label(): string
    {
        return sprintf('Tháng %d/%d', $this->month, $this->year);
    }
}

