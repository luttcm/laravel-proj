@extends('layouts.app')

@section('title', 'Новости')

@section('content')
<style>
body {
    background-color: #edeef0;
}

.news-feed {
    max-width: 600px;
    margin: 0 auto;
}

.news-card {
    background: #fff;
    border-radius: 8px;
    border: 1px solid #dce1e6;
    margin-bottom: 16px;
    overflow: hidden;
}

.news-card-header {
    padding: 12px 16px;
    border-bottom: 1px solid #dce1e6;
}

.news-title {
    font-size: 16px;
    font-weight: 600;
    color: #222;
    margin: 0;
}

.news-image-container {
    width: 100%;
    aspect-ratio: 1 / 1;
    overflow: hidden;
}

.news-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.news-card-text {
    padding: 12px 16px;
}

.news-text {
    font-size: 14px;
    color: #333;
    line-height: 1.5;
    margin: 0;
}

.news-card-footer {
    padding: 8px 12px;
    border-top: 1px solid #dce1e6;
    display: flex;
    gap: 8px;
    align-items: center;
}

.news-actions .btn {
    border: none;
    background: transparent;
    font-size: 13px;
    color: #626d7a;
    padding: 6px 8px;
    flex: 1;
    text-align: left;
    display: flex;
    align-items: center;
    gap: 4px;
}

.news-actions .btn:hover {
    color: #0077ff;
    border-radius: 4px;
}

.news-add-btn {
    background-color: #0077ff;
    border: none;
    font-size: 14px;
    padding: 8px 16px;
    color: #fff;
}

.news-add-btn:hover {
    background-color: #006ae6;
}
</style>
<div class="container my-4">
    <div class="news-feed">

        @if(auth()->user() && in_array(auth()->user()->role, ['admin','redactor']))
            <div class="mb-3 text-end">
                <a href="{{ route('news.create') }}" class="btn news-add-btn text-white">
                    Добавить новость
                </a>
            </div>
        @endif

        @forelse($news as $item)
            <div class="news-card">
                
                @if($item->firstPicture)
                    <div class="news-image-container">
                        <img src="{{ asset($item->firstPicture->path) }}" class="news-image">
                    </div>
                @endif

                <div class="news-card-header">
                    <h3 class="news-title">{{ $item->title }}</h3>
                </div>

                <div class="news-card-text">
                    <p class="news-text">
                        {{ Illuminate\Support\Str::limit(strip_tags($item->content), 250) }}
                    </p>
                </div>

                <div class="news-card-footer news-actions">
                    <button class="btn js-like-button" data-url="{{ route('news.like', ['id' => $item->id]) }}" data-id="{{ $item->id }}" style="flex:1;">
                        <img src="{{ asset('app/public/img/like.png') }}" alt="Logo" style="height:15px; width: 15px;">
                        <span class="js-like-count">{{ $item->reactions }}</span>
                    </button>

                    <a href="{{ route('news.show', ['id' => $item->id]) }}" class="btn">
                        Читать дальше
                    </a>

                    @if(auth()->user() && in_array(auth()->user()->role, ['admin','manager']))
                        <a href="{{ route('news.edit', ['id' => $item->id]) }}" class="btn" style="padding: 6px 4px;">
                            Редактировать
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-4">
                Нет новостей
            </div>
        @endforelse

    </div>
</div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        document.body.addEventListener('click', function (e) {
            const btn = e.target.closest('.js-like-button');
            if (!btn) return;
            e.preventDefault();

            const url = btn.dataset.url;
            const countEl = btn.querySelector('.js-like-count');
            btn.disabled = true;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({})
            }).then(res => res.json())
            .then(data => {
                if (data && typeof data.reactions !== 'undefined') {
                    countEl.textContent = data.reactions;
                    if (data.liked) btn.classList.add('liked'); else btn.classList.remove('liked');
                }
            }).catch(err => console.error(err))
            .finally(() => btn.disabled = false);
        });
    });
    </script>

    @endsection
