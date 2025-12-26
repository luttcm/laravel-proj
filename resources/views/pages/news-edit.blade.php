@extends('layouts.app')

@section('title', 'Редактировать новость')

@section('content')
<div class="container" style="max-width: 700px;">
    <div class="news-card">
        <div class="news-card-body">
            <form method="POST" action="{{ route('news.update', ['id' => $news->id]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Название</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $news->title) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Контент</label>
                    <textarea name="content" class="form-control" rows="6" required>{{ old('content', $news->content) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Добавить картинки</label>
                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                </div>
                <div class="mb-3">
                    <label class="form-label">Существующие картинки</label>
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach($pictures as $p)
                            <img src="{{ asset($p->path) }}" style="width:120px;height:120px;object-fit:cover;border-radius:6px;">
                        @endforeach
+                    </div>
                </div>
                <button class="btn btn-primary">Сохранить</button>
            </form>
        </div>
    </div>
</div>
@endsection
