<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\AstrologyEngineInterface;
use App\Http\Requests\TuViRequest;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class TuViController extends Controller
{
    public function __construct(
        private readonly AstrologyEngineInterface $astrology,
    ) {}

    public function index(): Response
    {
        return Inertia::render('TuVi/Index', [
            'defaults' => [
                'gender' => 'male',
                'birth_date' => '1992-01-01',
            ],
            'result' => null,
        ]);
    }

    public function result(TuViRequest $request): Response
    {
        $validated = $request->validated();
        $birthDate = Carbon::createFromFormat('Y-m-d', (string) $validated['birth_date'], 'Asia/Ho_Chi_Minh');

        $chart = $this->astrology->calculateChart($birthDate, (string) $validated['gender']);
        $lunarDate = $this->astrology->getLunarDate($birthDate);

        return Inertia::render('TuVi/Index', [
            'defaults' => [
                'gender' => $validated['gender'],
                'birth_date' => $validated['birth_date'],
            ],
            'result' => [
                'chart' => $chart,
                'lunarDate' => $lunarDate,
                'note' => 'Lá số thể hiện xu hướng theo tử vi truyền thống, nên kết hợp thực tế cuộc sống để ra quyết định.',
            ],
        ]);
    }
}

