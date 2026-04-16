<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Analytics\PageViewService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackPageView
{
    public function __construct(
        private readonly PageViewService $pageViewService,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->isMethod('GET') || $this->shouldIgnorePath($request->getPathInfo())) {
            return $response;
        }

        try {
            $this->pageViewService->recordVisit($request);
        } catch (Throwable $exception) {
            Log::error('Failed to track page view', [
                'url' => $request->fullUrl(),
                'path' => $request->getPathInfo(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'error' => $exception->getMessage(),
            ]);
        }

        return $response;
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
            || str_starts_with($path, '/sitemap')
            || str_starts_with($path, '/_debugbar');
    }
}
