<?php

declare(strict_types=1);

namespace App\Services\Naming;

use App\Models\VietnameseNameDictionary;

class NameGeneratorService
{
    /**
     * @param list<string> $targetElements
     * @return array{
     *   suggestions: list<array{full_name:string, base_name:string, element:string, meaning:string, score:int}>,
     *   taboo_names: list<string>
     * }
     */
    public function generate(
        string $fatherName,
        string $motherName,
        string $babyGender,
        bool $includeMotherSurname,
        array $targetElements,
        array $allowedElements,
    ): array {
        $fatherParts = preg_split('/\s+/u', trim($fatherName)) ?: [];
        $motherParts = preg_split('/\s+/u', trim($motherName)) ?: [];
        $fatherSurname = $fatherParts[0] ?? 'Nguyễn';
        $motherSurname = $motherParts[0] ?? '';
        $tabooNames = array_filter([
            $fatherParts[count($fatherParts) - 1] ?? null,
            $motherParts[count($motherParts) - 1] ?? null,
        ]);

        $genderPool = $babyGender === 'male' ? ['male', 'unisex'] : ['female', 'unisex'];

        $query = VietnameseNameDictionary::query()
            ->whereIn('gender', $genderPool)
            ->whereIn('element', $allowedElements ?: ['kim', 'moc', 'thuy', 'hoa', 'tho']);

        if ($tabooNames) {
            $query->whereNotIn('name', $tabooNames);
        }

        $names = $query->get(['name', 'element', 'meaning']);
        /** @var array<string, array<string, list<string>>> $middleLibrary */
        $middleLibrary = config('naming_library.middle_names', []);
        $genderLibrary = $middleLibrary[$babyGender] ?? [];
        $defaultMiddleNames = $genderLibrary['default'] ?? ['An', 'Minh', 'Ngọc'];
        $suggestions = [];
        $seenFullNames = [];

        foreach ($names as $i => $name) {
            $priority = in_array($name->element, $targetElements, true) ? 20 : 0;
            $baseScore = 70 + $priority - ($i % 9);

            $middleCandidates = $this->pickMiddleNames(
                $genderLibrary[$name->element] ?? [],
                $defaultMiddleNames,
                $i,
            );

            foreach ($middleCandidates as $middleIndex => $middle) {
                $parts = [$fatherSurname];
                if ($includeMotherSurname && $motherSurname && $motherSurname !== $fatherSurname) {
                    $parts[] = $motherSurname;
                }
                $parts[] = $middle;
                $parts[] = $name->name;

                $fullName = implode(' ', array_filter($parts));
                if (isset($seenFullNames[$fullName])) {
                    continue;
                }
                $seenFullNames[$fullName] = true;

                $suggestions[] = [
                    'full_name' => $fullName,
                    'base_name' => $name->name,
                    'element' => $name->element,
                    'meaning' => $name->meaning,
                    'score' => max(50, min(99, $baseScore - ($middleIndex * 2))),
                ];
            }
        }

        usort($suggestions, static fn (array $a, array $b): int => $b['score'] <=> $a['score']);

        return [
            'suggestions' => array_slice($suggestions, 0, 18),
            'taboo_names' => array_values($tabooNames),
        ];
    }

    /**
     * @param list<string> $elementMiddleNames
     * @param list<string> $defaultMiddleNames
     * @return list<string>
     */
    private function pickMiddleNames(array $elementMiddleNames, array $defaultMiddleNames, int $seed): array
    {
        $merged = array_values(array_unique(array_merge($elementMiddleNames, $defaultMiddleNames)));
        if ($merged === []) {
            return ['An'];
        }

        $offset = $seed % count($merged);
        $rotated = array_merge(array_slice($merged, $offset), array_slice($merged, 0, $offset));

        return array_slice($rotated, 0, 2);
    }
}
