@extends('layouts.app')

@section('title', 'Создать новость')

@section('content')
<div class="container" style="max-width: 700px;">
    <div class="news-card">
        <div class="news-card-body">
            <form method="POST" action="{{ route('news.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Название новости</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Текст новости</label>
                    <textarea name="content" class="form-control" rows="6" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Изображения</label>
                    <i style="font-size: 12px; color: #626d7a">Для новостей подходит формат 1:1</i>
                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                </div>
                <button class="btn btn-primary">Создать</button>
            </form>
        </div>
    </div>
</div>
@endsection
