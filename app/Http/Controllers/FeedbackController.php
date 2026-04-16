<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Feedback;
use App\Services\FeedbackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class FeedbackController extends Controller
{
    public function __construct(
        private readonly FeedbackService $feedbackService,
    ) {}

    public function create(): Response
    {
        return Inertia::render('Feedback/Create', [
            'types' => Feedback::TYPES,
        ]);
    }

    public function store(StoreFeedbackRequest $request): RedirectResponse
    {
        if ($request->filled('website')) {
            Log::warning('Honeypot triggered on feedback submission.', [
                'ip' => $request->ip(),
                'website_value' => $request->input('website'),
            ]);

            return redirect()->route('feedback.create');
        }

        try {
            $validated = $request->validated();
            $ipHash = hash('sha256', (string) $request->ip());

            $this->feedbackService->store($validated, $ipHash);

            return redirect()->route('feedback.thanks');
        } catch (Throwable $exception) {
            Log::error('Feedback submission failed.', [
                'ip' => $request->ip(),
                'name' => $request->input('name'),
                'type' => $request->input('type'),
                'error' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('feedback.create')
                ->withErrors(['general' => 'Không thể gửi góp ý lúc này. Vui lòng thử lại sau.'])
                ->withInput($request->except('website'));
        }
    }

    public function thanks(): Response
    {
        return Inertia::render('Feedback/Thanks');
    }
}
