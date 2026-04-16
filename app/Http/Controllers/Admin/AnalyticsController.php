<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Analytics\PageViewService;
use Carbon\CarbonImmutable;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly PageViewService $pageViewService,
    ) {}

    public function index(): Response
    {
        $to = CarbonImmutable::now('Asia/Ho_Chi_Minh');
        $from = $to->subDays(6);

        return Inertia::render('Admin/Analytics/Index', [
            'summary' => $this->pageViewService->getSummary(),
            'dailyStats' => $this->pageViewService->getDailyStats($from, $to),
            'topPages' => $this->pageViewService->getTopPages(10),
        ]);
    }
}
