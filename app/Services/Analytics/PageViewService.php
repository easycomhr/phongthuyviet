<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\DailyStat;
use App\Models\PageView;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageViewService
{
    private const BOT_KEYWORDS = [
        'bot',
        'crawler',
        'spider',
        'googlebot',
        'bingbot',
        'slurp',
        'duckduck',
        'baidu',
        'yandex',
        'lighthouse',
        'headless',
    ];

    public function recordVisit(Request $request): void
    {
        $path = $request->getPathInfo();
        if ($this->shouldIgnorePath($path)) {
            return;
        }

        $userAgent = mb_strtolower((string) $request->userAgent());
        foreach (self::BOT_KEYWORDS as $keyword) {
            if (str_contains($userAgent, $keyword)) {
                return;
            }
        }

        PageView::query()->create([
            'url' => $path,
            'ip_hash' => hash('sha256', (string) $request->ip()),
            'user_agent' => $this->truncate((string) $request->userAgent(), 500),
            'referer' => $this->truncate((string) $request->headers->get('referer'), 500),
        ]);

        $today = Carbon::today('Asia/Ho_Chi_Minh');
        $this->updateDailyStats($today);
    }

    public function updateDailyStats(Carbon $date): void
    {
        $startOfDay = $date->copy()->timezone('Asia/Ho_Chi_Minh')->startOfDay();
        $endOfDay = $date->copy()->timezone('Asia/Ho_Chi_Minh')->endOfDay();

        $baseQuery = PageView::query()->whereBetween('created_at', [$startOfDay, $endOfDay]);

        $totalViews = (clone $baseQuery)->count();
        $uniqueVisitors = (clone $baseQuery)
            ->distinct('ip_hash')
            ->count('ip_hash');

        DB::table('daily_stats')->upsert([
            [
                'date' => $startOfDay->toDateString(),
                'total_views' => $totalViews,
                'unique_visitors' => $uniqueVisitors,
                'updated_at' => now(),
            ],
        ], ['date'], ['total_views', 'unique_visitors', 'updated_at']);
    }

    /**
     * @return array<int, array{date: string, views: int, visitors: int}>
     */
    public function getDailyStats(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $fromDate = $from->setTimezone('Asia/Ho_Chi_Minh')->startOfDay();
        $toDate = $to->setTimezone('Asia/Ho_Chi_Minh')->startOfDay();

        $statsByDate = DailyStat::query()
            ->whereBetween('date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->orderBy('date')
            ->get(['date', 'total_views', 'unique_visitors'])
            ->keyBy(fn (DailyStat $stat): string => $stat->date->toDateString());

        $result = [];
        for ($date = $fromDate; $date->lessThanOrEqualTo($toDate); $date = $date->addDay()) {
            $dateKey = $date->toDateString();
            $stat = $statsByDate->get($dateKey);

            $result[] = [
                'date' => $dateKey,
                'views' => (int) ($stat?->total_views ?? 0),
                'visitors' => (int) ($stat?->unique_visitors ?? 0),
            ];
        }

        return $result;
    }

    /**
     * @return array<int, array{url: string, views: int}>
     */
    public function getTopPages(int $limit = 10): array
    {
        $fromDate = CarbonImmutable::now('Asia/Ho_Chi_Minh')->subDays(29)->startOfDay();

        return PageView::query()
            ->selectRaw('url, COUNT(*) as views')
            ->where('created_at', '>=', $fromDate)
            ->groupBy('url')
            ->orderByDesc('views')
            ->limit($limit)
            ->get()
            ->map(static fn (PageView $pageView): array => [
                'url' => (string) $pageView->url,
                'views' => (int) $pageView->views,
            ])
            ->all();
    }

    /**
     * @return array{
     *     today: array{views: int, visitors: int},
     *     week: array{views: int, visitors: int},
     *     month: array{views: int, visitors: int},
     *     total: int
     * }
     */
    public function getSummary(): array
    {
        $now = CarbonImmutable::now('Asia/Ho_Chi_Minh');

        return [
            'today' => $this->getPeriodStats($now->startOfDay(), $now->endOfDay()),
            'week' => $this->getPeriodStats($now->subDays(6)->startOfDay(), $now->endOfDay()),
            'month' => $this->getPeriodStats($now->subDays(29)->startOfDay(), $now->endOfDay()),
            'total' => PageView::query()->count(),
        ];
    }

    private function shouldIgnorePath(string $path): bool
    {
        if ($path === '/robots.txt') {
            return true;
        }

        return $path === '/admin'
            || str_starts_with($path, '/admin/')
            || $path === '/api'
            || str_starts_with($path, '/api/')
            || str_starts_with($path, '/sitemap');
    }

    /**
     * @return array{views: int, visitors: int}
     */
    private function getPeriodStats(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $query = PageView::query()->whereBetween('created_at', [$from, $to]);

        return [
            'views' => (int) (clone $query)->count(),
            'visitors' => (int) (clone $query)->distinct('ip_hash')->count('ip_hash'),
        ];
    }

    private function truncate(string $value, int $maxLength): ?string
    {
        if ($value === '') {
            return null;
        }

        if (mb_strlen($value) <= $maxLength) {
            return $value;
        }

        return mb_substr($value, 0, $maxLength);
    }
}
