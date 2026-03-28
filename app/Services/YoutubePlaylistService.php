<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class YoutubePlaylistService
{
    public function searchPlaylists(string $query, int $limit = 2): array
    {
        $apiKey = config('services.youtube.api_key');

        if (blank($apiKey)) {
            throw new RuntimeException('YouTube API key is missing.');
        }

        $response = Http::timeout(30)
            ->retry(3, 1500)
            ->get('https://www.googleapis.com/youtube/v3/search', [
                'key' => $apiKey,
                'part' => 'snippet',
                'q' => $query,
                'type' => 'playlist',
                'maxResults' => 2,
            ])
            ->throw()
            ->json();

        return collect($response['items'] ?? [])
            ->map(function ($item) {
                return [
                    'youtube_playlist_id' => data_get($item, 'id.playlistId'),
                    'title' => html_entity_decode((string) data_get($item, 'snippet.title', '')),
                    'description' => html_entity_decode((string) data_get($item, 'snippet.description', '')),
                    'thumbnail_url' => data_get($item, 'snippet.thumbnails.high.url')
                        ?? data_get($item, 'snippet.thumbnails.medium.url')
                        ?? data_get($item, 'snippet.thumbnails.default.url'),
                    'channel_name' => html_entity_decode((string) data_get($item, 'snippet.channelTitle', '')),
                ];
            })
            ->filter(fn($item) => filled($item['youtube_playlist_id']))
            ->unique('youtube_playlist_id')
            ->take($limit)
            ->values()
            ->all();
    }
}
