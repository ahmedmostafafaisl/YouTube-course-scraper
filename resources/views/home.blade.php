@extends('layouts.app')

@section('content')
    <div class="container">
        <section class="scraper-hero mb-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <span class="badge bg-light text-dark mb-3">AI + YouTube</span>
                    <h1 class="display-6 fw-bold mb-3">جمع الدورات التعليمية من يوتيوب</h1>
                    <p class="mb-0 opacity-75">
                        اكتب التصنيفات، وسيتم توليد عناوين بحث تلقائيًا ثم جلب الـ playlists التعليمية وحفظها بدون تكرار.
                    </p>
                </div>

                <div class="col-lg-6">
                    <div class="panel-card p-4">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger mb-3">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form action="{{ route('fetch.store') }}" method="POST">
                            @csrf

                            <label for="categories_text" class="form-label fw-semibold">
                                التصنيفات (كل تصنيف في سطر)
                            </label>

                            <textarea id="categories_text" name="categories_text" rows="8"
                                class="form-control form-control-lg mb-3">{{ old('categories_text', "Marketing\nProgramming\nGraphic Design\nBusiness\nEngineering") }}</textarea>

                            <button class="btn btn-danger btn-lg w-100">
                                ابدأ الجمع
                            </button>


                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                <div>
                    <h2 class="h3 fw-bold mb-1">الدورات المكتشفة</h2>
                    <p class="text-muted mb-0">إجمالي النتائج: {{ $playlists->total() }}</p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('home') }}"
                        class="btn btn-sm {{ request('category') ? 'btn-outline-secondary' : 'btn-danger' }}">
                        الكل
                    </a>

                    @foreach($categories as $category)
                        <a href="{{ route('home', ['category' => $category->id]) }}"
                            class="btn btn-sm {{ (string) request('category') === (string) $category->id ? 'btn-danger' : 'btn-outline-secondary' }}">
                            {{ $category->name }} ({{ $category->playlists_count }})
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="row g-4">
                @forelse($playlists as $playlist)
                    <div class="col-md-6 col-xl-4">
                        <div class="card playlist-card h-100">
                            @if($playlist->thumbnail_url)
                                <img src="{{ $playlist->thumbnail_url }}" class="card-img-top playlist-thumb"
                                    alt="{{ $playlist->title }}">
                            @endif

                            <div class="card-body d-flex flex-column">
                                <h3 class="h5 fw-bold mb-2">{{ $playlist->title }}</h3>

                                <p class="text-muted small mb-2">
                                    {{ \Illuminate\Support\Str::limit($playlist->description, 140) }}
                                </p>

                                <p class="small mb-3">
                                    القناة: <strong>{{ $playlist->channel_name ?: 'غير معروف' }}</strong>
                                </p>

                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @foreach($playlist->categories as $category)
                                        <span class="badge text-bg-light border">{{ $category->name }}</span>
                                    @endforeach
                                </div>

                                <a href="{{ $playlist->youtube_url }}" target="_blank" rel="noopener"
                                    class="btn btn-outline-danger mt-auto">
                                    فتح الـ Playlist
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="panel-card p-5 text-center">
                            <h3 class="h5 fw-bold mb-2">لسه مفيش نتائج</h3>
                            <p class="text-muted mb-0">ابدأ بإدخال التصنيفات ثم اضغط ابدأ الجمع.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($playlists->hasPages())
                <div class="mt-4">
                    {{ $playlists->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
