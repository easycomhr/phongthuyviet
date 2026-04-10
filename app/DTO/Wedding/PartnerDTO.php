<?php

declare(strict_types=1);

namespace App\DTO\Wedding;

use Carbon\CarbonImmutable;

class PartnerDTO
{
    /**
     * @param 'male'|'female' $gender
     */
    public function __construct(
        public readonly string $name,
        public readonly string $gender,
        public readonly CarbonImmutable $birthDate,
    ) {}

    /**
     * @param array{name: string, gender: 'male'|'female', birth_date: string} $input
     */
    public static function fromArray(array $input): self
    {
        return new self(
            trim($input['name']),
            $input['gender'],
            CarbonImmutable::createFromFormat('Y-m-d', $input['birth_date'], 'Asia/Ho_Chi_Minh'),
        );
    }
}

