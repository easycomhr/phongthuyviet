<?php

declare(strict_types=1);

namespace App\DTO\Astrology;

class StarDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $quality,
        public readonly string $element,
    ) {}

    /**
     * @return array{name: string, quality: string, element: string}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'quality' => $this->quality,
            'element' => $this->element,
        ];
    }
}

