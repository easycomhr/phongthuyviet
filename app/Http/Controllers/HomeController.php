<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SearchDayRequest;
use App\Models\EventCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $categories = EventCategory::popular()->get(['id', 'name', 'slug', 'icon']);

        return Inertia::render('Home', [
            'categories' => $categories,
        ]);
    }

    public function search(SearchDayRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $monthYear = substr((string) $validated['date'], 0, 7);

        return redirect()->route('gooddays.index', [
            'category_slug' => $validated['category_slug'],
            'month_year'    => $monthYear,
        ]);
    }
}
