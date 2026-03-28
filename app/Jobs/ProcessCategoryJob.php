<?php

namespace App\Jobs;

use App\Models\Category;
use App\Services\AiCourseTitleService;
use App\Services\PlaylistStorageService;
use App\Services\YoutubePlaylistService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCategoryJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;
    public int $uniqueFor = 3600;

    public function __construct(public int $categoryId) {}

    public function uniqueId(): string
    {
        return (string) $this->categoryId;
    }

    public function handle(
        AiCourseTitleService $aiCourseTitleService,
        YoutubePlaylistService $youtubePlaylistService,
        PlaylistStorageService $playlistStorageService
    ): void {
        $category = Category::findOrFail($this->categoryId);

        $queries = $aiCourseTitleService->generate(
            $category->name,
            config('services.scraper.ai_queries_per_category', 2)
        );

        foreach ($queries as $query) {
            try {
                $playlists = $youtubePlaylistService->searchPlaylists(
                    $query,
                    (int) config('services.scraper.youtube_results_per_query', 2)
                );

                $playlistStorageService->storeMany($category, $playlists, $query);
            } catch (\Throwable $e) {
                report($e);
                continue;
            }
        }
    }
}
