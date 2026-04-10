<?php

declare(strict_types=1);

namespace App\Services\Wedding\Filters;

interface WeddingFilterInterface
{
    /**
     * @param array<string, mixed> $state
     * @return array<string, mixed>
     */
    public function apply(array $state): array;
}

