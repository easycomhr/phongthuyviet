<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Feedback;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Throwable;

class FeedbackService
{
    public function store(array $data, string $ipHash): Feedback
    {
        try {
            $feedback = Feedback::query()->create([
                'name' => (string) $data['name'],
                'email' => $data['email'] ?? null,
                'type' => (string) $data['type'],
                'content' => (string) $data['content'],
                'ip_hash' => $ipHash,
            ]);

            Log::info('Feedback created successfully.', [
                'name' => $feedback->name,
                'type' => $feedback->type,
            ]);

            return $feedback;
        } catch (Throwable $exception) {
            Log::error('Failed to store feedback.', [
                'name' => $data['name'] ?? null,
                'type' => $data['type'] ?? null,
                'ip_hash' => $ipHash,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function getAll(int $perPage = 20): LengthAwarePaginator
    {
        try {
            return Feedback::query()
                ->orderByDesc('created_at')
                ->paginate($perPage);
        } catch (Throwable $exception) {
            Log::error('Failed to retrieve feedbacks.', [
                'per_page' => $perPage,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
