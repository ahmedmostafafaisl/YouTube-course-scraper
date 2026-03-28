<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::withCount('playlists')
            ->orderByDesc('playlists_count')
            ->orderBy('name')
            ->get();

        $playlists = Playlist::with('categories')
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('categories', function ($subQuery) use ($request) {
                    $subQuery->where('categories.id', $request->integer('category'));
                });
            })
            ->latest('updated_at')
            ->paginate(12)
            ->withQueryString();

        return view('home', compact('categories', 'playlists'));
    }
}
