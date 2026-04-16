<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FeedbackService;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class FeedbacksController extends Controller
{
    public function __construct(
        private readonly FeedbackService $feedbackService,
    ) {}

    public function index(): Response
    {
        try {
            $feedbacks = $this->feedbackService->getAll();

            return Inertia::render('Admin/Feedbacks/Index', [
                'feedbacks' => $feedbacks,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to load admin feedback list.', [
                'error' => $exception->getMessage(),
            ]);

            return Inertia::render('Admin/Feedbacks/Index', [
                'feedbacks' => [
                    'data' => [],
                    'links' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 20,
                ],
            ]);
        }
    }
}
