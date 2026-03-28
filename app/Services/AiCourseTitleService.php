<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class AiCourseTitleService
{
    public function generate(string $category, int $count = 10): array
    {
        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-2.5-flash');

        if (blank($apiKey)) {
            throw new RuntimeException('Gemini API key is missing.');
        }

        $prompt = "Generate {$count} educational YouTube course or playlist search queries for the category: {$category}. "
            . "Return JSON array only. No markdown. "
            . "Keep them realistic and useful for searching YouTube playlists. "
            . "Mix Arabic and English when useful.";

        $response = Http::timeout(60)
            ->retry(3, 2000)
            ->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                ]
            )
            ->throw()
            ->json();

        $text = data_get($response, 'candidates.0.content.parts.0.text', '');

        $text = trim((string) $text);
        $text = preg_replace('/```json|```/i', '', $text);

        $decoded = json_decode($text, true);

        if (! is_array($decoded)) {
            $decoded = preg_split('/\r\n|\r|\n/', $text);
        }

        return collect($decoded)
            ->filter(fn($item) => is_string($item))
            ->map(fn($item) => trim($item))
            ->filter()
            ->unique()
            ->take($count)
            ->values()
            ->all();
    }
}
