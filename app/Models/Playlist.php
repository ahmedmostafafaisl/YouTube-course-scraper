<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'youtube_playlist_id',
        'title',
        'description',
        'thumbnail_url',
        'channel_name',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)
            ->withPivot('source_query')
            ->withTimestamps();
    }

    public function getYoutubeUrlAttribute(): string
    {
        return 'https://www.youtube.com/playlist?list=' . $this->youtube_playlist_id;
    }
}
