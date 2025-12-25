@extends('layouts.app')

@section('title', 'Новости')

@section('content')
<link href="/css/news.css" rel="stylesheet">
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
                        <img src="{{ asset('img/like.png') }}" alt="Logo" style="height:15px; width: 15px;">
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
