<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EventCategory;
use App\Models\Prayer;
use App\Services\GoodDayCalculatorService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class GoodDayController extends Controller
{
    private const EVENT_TO_PRAYER_CATEGORY = [
        'khoi-cong' => 'dong-tho',
        'dong-tho' => 'dong-tho',
        'khai-truong' => 'khai-truong',
        'nhap-trach' => 'nhap-trach',
        'cuoi-hoi' => 'cuoi-hoi',
        'xuat-hanh' => 'xuat-hanh',
        'ma-chay' => 'hieu-le-tang-nghi',
    ];

    public function __construct(
        private readonly GoodDayCalculatorService $calculator,
    ) {}

    /**
     * @throws ValidationException
     */
    public function index(Request $request): Response
    {
        $validated = $request->validate([
            'category_slug' => ['nullable', 'exists:event_categories,slug'],
            'month_year'    => ['nullable', 'date_format:Y-m'],
            'date'          => ['nullable', 'date_format:Y-m-d'],
        ]);

        $categories = EventCategory::query()
            ->orderByDesc('is_popular')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon']);

        $defaultCategorySlug = EventCategory::popular()->value('slug')
            ?? EventCategory::query()->value('slug');

        $categorySlug = $validated['category_slug'] ?? $defaultCategorySlug;
        $requestedDate = $validated['date'] ?? null;
        $monthYear = $validated['month_year']
            ?? ($requestedDate ? substr($requestedDate, 0, 7) : now('Asia/Ho_Chi_Minh')->format('Y-m'));

        [$year, $month] = array_map('intval', explode('-', $monthYear));
        $days = $this->calculator->getMonthDays($categorySlug, $year, $month);

        $category = EventCategory::where('slug', $categorySlug)->firstOrFail();
        $today = CarbonImmutable::now('Asia/Ho_Chi_Minh')->format('Y-m-d');

        $selectedDate = $requestedDate
            ?? (
                str_starts_with($today, $monthYear)
                    ? $today
                    : ($days[0]['date'] ?? null)
            );

        $selectedDay = null;
        foreach ($days as $day) {
            if ($day['date'] === $selectedDate) {
                $selectedDay = $day;
                break;
            }
        }

        return Inertia::render('GoodDays/Index', [
            'days'       => $days,
            'category'   => $category,
            'monthYear'  => $monthYear,
            'categories' => $categories,
            'selectedDate' => $selectedDate,
            'selectedDay'  => $selectedDay,
            'prayerGuide'  => $this->resolvePrayerGuide($categorySlug),
        ]);
    }

    /**
     * @return array{title: string, slug: string, content: string, category_name: string}|null
     */
    private function resolvePrayerGuide(string $eventCategorySlug): ?array
    {
        $prayerCategorySlug = self::EVENT_TO_PRAYER_CATEGORY[$eventCategorySlug] ?? null;
        if (!$prayerCategorySlug) {
            return null;
        }

        $prayer = Prayer::query()
            ->with('prayerCategory:id,name,slug')
            ->whereHas('prayerCategory', function ($q) use ($prayerCategorySlug) {
                $q->where('slug', $prayerCategorySlug);
            })
            ->orderBy('id')
            ->first(['id', 'category_id', 'title', 'slug', 'content']);

        if (!$prayer) {
            return null;
        }

        return [
            'title' => $prayer->title,
            'slug' => $prayer->slug,
            'content' => $prayer->content,
            'category_name' => (string) ($prayer->prayerCategory->name ?? 'Văn khấn'),
        ];
    }
}
