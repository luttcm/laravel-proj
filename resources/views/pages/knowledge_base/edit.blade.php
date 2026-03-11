@extends('layouts.app')

@section('title', 'Редактировать страницу БЗ')

@section('content')
<div class="container px-4 my-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 8px;">Редактировать: {{ $page->title }}</h1>
                    <p class="text-muted">Внесите изменения в страницу</p>
                </div>
                <a href="{{ route('knowledge-base.index', ['page' => $page->id]) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Назад
                </a>
            </div>

            <div class="card shadow-sm border-0" style="border-radius: 12px; padding: 24px;">
                <form action="{{ route('knowledge-base.update', $page->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="title" class="form-label" style="font-weight: 600;">Заголовок</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $page->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="category_id" class="form-label" style="font-weight: 600;">Раздел (необязательно)</label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">Без раздела</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $page->category_id) == $category->id ? 'selected' : '' }}>
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
                        <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $page->order) }}">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="photo" class="form-label" style="font-weight: 600;">Прикрепить новое фото (необязательно)</label>
                        @if($page->photo_path)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $page->photo_path) }}" alt="Текущее фото" style="max-height: 150px; border-radius: 6px; border: 1px solid #ddd; padding: 4px;">
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="remove_photo">
                                <label class="form-check-label text-danger" for="remove_photo">
                                    Удалить текущее фото
                                </label>
                            </div>
                        @endif
                        <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label" style="font-weight: 600;">Содержание</label>
                        <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="15" required>{{ old('content', $page->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary px-5 py-2" style="border-radius: 6px; font-weight: 600;">
                            Обновить
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
