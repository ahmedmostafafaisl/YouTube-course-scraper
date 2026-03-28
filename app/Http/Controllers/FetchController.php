<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFetchRequest;
use App\Jobs\ProcessCategoryJob;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;

class FetchController extends Controller
{
    public function store(StoreFetchRequest $request): RedirectResponse
    {
        $categories = collect(preg_split('/\r\n|\r|\n/', $request->input('categories_text')))
            ->map(fn($item) => preg_replace('/\s+/u', ' ', trim($item)))
            ->filter()
            ->unique(fn($item) => mb_strtolower($item))
            ->values();

        if ($categories->isEmpty()) {
            return back()->withErrors([
                'categories_text' => 'من فضلك أدخل تصنيف واحد على الأقل.',
            ])->withInput();
        }

        foreach ($categories as $name) {
            $category = Category::firstOrCreate(['name' => $name]);

            if ($category->playlists()->exists()) {
                continue;
            }

            ProcessCategoryJob::dispatch($category->id);
        }

        return redirect()
            ->route('home')
            ->with('success', 'تم إرسال التصنيفات للمعالجة. تابع النتائج بعد لحظات.');
    }
}
