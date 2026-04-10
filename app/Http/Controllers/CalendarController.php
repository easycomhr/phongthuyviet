<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\FengShuiDailyService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CalendarController extends Controller
{
    public function __construct(
        private readonly FengShuiDailyService $fengShui
    ) {}

    /**
     * @throws ValidationException
     */
    public function today(Request $request): Response
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $date = isset($validated['date'])
            ? CarbonImmutable::createFromFormat('Y-m-d', $validated['date'], 'Asia/Ho_Chi_Minh')
            : CarbonImmutable::now('Asia/Ho_Chi_Minh');

        return Inertia::render('Calendar/Today', [
            'daily'    => $this->fengShui->getDaily($date),
            'prevDate' => $date->subDay()->format('Y-m-d'),
            'nextDate' => $date->addDay()->format('Y-m-d'),
        ]);
    }
}
