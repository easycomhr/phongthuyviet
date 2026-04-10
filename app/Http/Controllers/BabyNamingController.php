<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\BabyNamingRequest;
use App\Services\Naming\MissingElementCalculator;
use App\Services\Naming\NameGeneratorService;
use App\Services\Naming\ParentCompatibilityService;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class BabyNamingController extends Controller
{
    public function __construct(
        private readonly MissingElementCalculator $missingElement,
        private readonly ParentCompatibilityService $parentCompatibility,
        private readonly NameGeneratorService $nameGenerator,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Naming/Index', [
            'defaults' => [
                'father_name' => 'Nguyễn Văn Minh',
                'father_birth_year' => 1993,
                'mother_name' => 'Lê Thị Lan',
                'mother_birth_year' => 1995,
                'baby_gender' => 'male',
                'baby_birth_date' => '2026-10-20 09:30',
                'include_mother_surname' => true,
            ],
            'result' => null,
        ]);
    }

    public function result(BabyNamingRequest $request): Response
    {
        $validated = $request->validated();
        $babyBirthDate = Carbon::createFromFormat('Y-m-d H:i', (string) $validated['baby_birth_date'], 'Asia/Ho_Chi_Minh');
        $parent = $this->parentCompatibility->analyze(
            (int) $validated['father_birth_year'],
            (int) $validated['mother_birth_year'],
        );
        $missing = $this->missingElement->analyze($babyBirthDate, $parent['allowed_elements']);
        $generated = $this->nameGenerator->generate(
            (string) $validated['father_name'],
            (string) $validated['mother_name'],
            (string) $validated['baby_gender'],
            (bool) ($validated['include_mother_surname'] ?? false),
            $missing['target_elements'],
            $parent['allowed_elements'],
        );

        return Inertia::render('Naming/Index', [
            'defaults' => [
                ...$validated,
                'include_mother_surname' => (bool) ($validated['include_mother_surname'] ?? false),
            ],
            'result' => [
                'analysis' => [
                    'bazi' => $missing['bazi'],
                    'targetElements' => $missing['target_elements'],
                    'fallbackUsed' => $missing['fallback_used'],
                    'parent' => $parent,
                    'message' => $missing['fallback_used']
                        ? 'Tứ trụ của bé khá cân bằng, hệ thống ưu tiên hành tương sinh với bố mẹ để tăng hòa khí gia đạo.'
                        : 'Hệ thống ưu tiên tên mang hành bổ khuyết theo Bát Tự của bé và tương sinh với bố mẹ.',
                ],
                'suggestions' => $generated['suggestions'],
                'tabooNames' => $generated['taboo_names'],
            ],
        ]);
    }
}

