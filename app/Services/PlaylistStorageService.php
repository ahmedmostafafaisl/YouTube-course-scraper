<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Playlist;
use Illuminate\Support\Arr;

class PlaylistStorageService
{
    public function storeMany(Category $category, array $playlists, string $sourceQuery): void
    {
        foreach ($playlists as $data) {
            $playlist = Playlist::updateOrCreate(
                ['youtube_playlist_id' => $data['youtube_playlist_id']],
                Arr::except($data, ['youtube_playlist_id'])
            );

            $category->playlists()->syncWithoutDetaching([
                $playlist->id => ['source_query' => $sourceQuery],
            ]);
        }
    }
}
