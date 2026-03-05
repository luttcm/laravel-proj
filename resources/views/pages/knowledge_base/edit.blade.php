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
                <form action="{{ route('knowledge-base.update', $page->id) }}" method="POST">
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
                        <label for="order" class="form-label" style="font-weight: 600;">Порядок (для сортировки)</label>
                        <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $page->order) }}">
                        @error('order')
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
