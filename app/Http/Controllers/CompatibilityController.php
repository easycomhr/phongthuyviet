<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CompatibilityRequest;
use App\Services\CompatibilityService;
use Inertia\Inertia;
use Inertia\Response;

class CompatibilityController extends Controller
{
    public function __construct(
        private readonly CompatibilityService $compatibility,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Age/Index', [
            'defaults' => [
                'person_a_gender' => 'male',
                'person_b_gender' => 'female',
            ],
        ]);
    }

    public function result(CompatibilityRequest $request): Response
    {
        $validated = $request->validated();

        return Inertia::render('Age/Result', [
            'input'  => $validated,
            'result' => $this->compatibility->analyze(
                $validated['person_a_gender'],
                $validated['person_a_birth_date'],
                $validated['person_b_gender'],
                $validated['person_b_birth_date'],
            ),
        ]);
    }
}
