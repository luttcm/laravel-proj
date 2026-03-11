@extends('layouts.app')

@section('title', 'Создать страницу БЗ')

@section('content')
<div class="container px-4 my-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 8px;">Создать страницу Базы Знаний</h1>
                    <p class="text-muted">Заполните данные для новой страницы</p>
                </div>
                <a href="{{ route('knowledge-base.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Назад
                </a>
            </div>

            <div class="card shadow-sm border-0" style="border-radius: 12px; padding: 24px;">
                <form action="{{ route('knowledge-base.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="title" class="form-label" style="font-weight: 600;">Заголовок</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="category_id" class="form-label" style="font-weight: 600;">Раздел (необязательно)</label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">Без раздела</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="order" class="form-label" style="font-weight: 600;">Порядок (для сортировки)</label>
                        <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', 0) }}">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="photo" class="form-label" style="font-weight: 600;">Прикрепить фото (необязательно)</label>
                        <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label" style="font-weight: 600;">Содержание</label>
                        <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="15" required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <p class="text-muted small mt-2">
                            <i class="bi bi-info-circle me-1"></i> Можно использовать простые переносы строк. HTML-теги будут экранированы для безопасности.
                        </p>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary px-5 py-2" style="border-radius: 6px; font-weight: 600;">
                            Сохранить
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
