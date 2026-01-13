@extends('layouts.app')

@section('title', $newsItem->title)

@section('content')
<div class="card">
    <div class="card-body">
        <h1 class="card-title">{{ $newsItem->title }}</h1>
        <p class="text-muted small">
            Автор: {{ $newsItem->author ? $newsItem->author->name . ' (' . $newsItem->author->role . ')' : '—' }}
            • {{ $newsItem->created_at->format('d.m.Y H:i') }}
        </p>

        @if(isset($pictures) && $pictures->count())
            <div class="mb-4">
                <div class="row g-2">
                    @foreach($pictures as $pic)
                        <div class="col-md-4">
                            <img 
                                src="{{ asset($pic->path) }}" 
                                class="img-fluid rounded cursor-pointer" 
                                style="height:150px; object-fit:cover; cursor:pointer;" 
                                alt=""
                                data-bs-toggle="modal" 
                                data-bs-target="#photoModal"
                                onclick="setModalImage('{{ asset($pic->path) }}')">
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="modal fade" id="photoModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img id="modalImage" src="" class="img-fluid" alt="Увеличенное фото">
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function setModalImage(src) {
                    document.getElementById('modalImage').src = src;
                }
            </script>
        @endif

        <div class="mt-3">
            {!! nl2br(e($newsItem->content)) !!}
        </div>

        <div class="mt-4">
            <a href="{{ Route::has('news.index') ? route('news.index') : url('/news') }}" class="btn btn-secondary">Назад к новостям</a>
        </div>
    </div>
</div>
@endsection
