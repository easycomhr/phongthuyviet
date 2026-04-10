<?php

declare(strict_types=1);

namespace App\Services\Naming;

use App\Services\LunarCalendarService;
use Carbon\CarbonImmutable;

class ParentCompatibilityService
{
    /** @var array<string, string> */
    private const STEM_TO_ELEMENT = [
        'Giáp' => 'moc', 'Ất' => 'moc',
        'Bính' => 'hoa', 'Đinh' => 'hoa',
        'Mậu' => 'tho', 'Kỷ' => 'tho',
        'Canh' => 'kim', 'Tân' => 'kim',
        'Nhâm' => 'thuy', 'Quý' => 'thuy',
    ];

    /** @var array<string, string> */
    private const GENERATES = [
        'kim' => 'thuy',
        'thuy' => 'moc',
        'moc' => 'hoa',
        'hoa' => 'tho',
        'tho' => 'kim',
    ];

    /** @var array<string, string> */
    private const OVERCOMES = [
        'kim' => 'moc',
        'moc' => 'tho',
        'tho' => 'thuy',
        'thuy' => 'hoa',
        'hoa' => 'kim',
    ];

    public function __construct(
        private readonly LunarCalendarService $lunar,
    ) {}

    /**
     * @return array{
     *   father_element: string,
     *   mother_element: string,
     *   allowed_elements: list<string>,
     *   blocked_elements: list<string>
     * }
     */
    public function analyze(int $fatherBirthYear, int $motherBirthYear): array
    {
        $fatherElement = $this->yearElement($fatherBirthYear);
        $motherElement = $this->yearElement($motherBirthYear);

        $elements = ['kim', 'moc', 'thuy', 'hoa', 'tho'];
        $allowed = [];
        $blocked = [];

        foreach ($elements as $candidate) {
            $okWithFather = $this->isHarmonious($candidate, $fatherElement);
            $okWithMother = $this->isHarmonious($candidate, $motherElement);

            if ($okWithFather && $okWithMother) {
                $allowed[] = $candidate;
            } else {
                $blocked[] = $candidate;
            }
        }

        return [
            'father_element' => $fatherElement,
            'mother_element' => $motherElement,
            'allowed_elements' => $allowed,
            'blocked_elements' => $blocked,
        ];
    }

    private function yearElement(int $year): string
    {
        $date = CarbonImmutable::create($year, 7, 1, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $can = $this->lunar->getCanChi($date)['yearCan'];

        return self::STEM_TO_ELEMENT[$can] ?? 'tho';
    }

    private function isHarmonious(string $child, string $parent): bool
    {
        if ($child === $parent) {
            return true;
        }

        if ((self::GENERATES[$child] ?? null) === $parent || (self::GENERATES[$parent] ?? null) === $child) {
            return true;
        }

        if ((self::OVERCOMES[$child] ?? null) === $parent || (self::OVERCOMES[$parent] ?? null) === $child) {
            return false;
        }

        return true;
    }
}

