<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Prayer;
use Carbon\CarbonImmutable;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $prayers = Prayer::select('slug', 'updated_at')->get();
        $now = CarbonImmutable::now('Asia/Ho_Chi_Minh')->toAtomString();

        $staticRoutes = [
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['url' => '/lich-hom-nay', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => '/tra-cuu-ngay-tot', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => '/xem-tuoi', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/huong-nha', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/tu-vi', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/ngay-cuoi', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/dat-ten-cho-con', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => '/van-khan', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ];

        $content = view('sitemap', [
            'staticRoutes' => $staticRoutes,
            'prayers' => $prayers,
            'now' => $now,
            'baseUrl' => config('app.url'),
        ])->render();

        return response($content, 200, ['Content-Type' => 'application/xml']);
    }
}
