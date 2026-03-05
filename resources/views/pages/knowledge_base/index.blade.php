@extends('layouts.app')

@section('title', 'База знаний')

@section('content')
<div class="container-fluid px-4 my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" style="font-weight: 600;">Разделы</h5>
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'redactor')
                        <a href="{{ route('knowledge-base.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg"></i>
                        </a>
                    @endif
                </div>
                <div class="list-group list-group-flush">
                    @forelse($pages as $page)
                        <a href="{{ route('knowledge-base.index', ['page' => $page->id]) }}" 
                           class="list-group-item list-group-item-action border-0 py-3 {{ $selectedPage && $selectedPage->id === $page->id ? 'active' : '' }}"
                           style="{{ $selectedPage && $selectedPage->id === $page->id ? 'background-color: #f0f7ff; color: #0d6efd; border-left: 4px solid #0d6efd !important;' : 'border-left: 4px solid transparent !important;' }}">
                            {{ $page->title }}
                        </a>
                    @empty
                        <div class="p-4 text-center text-muted">
                            Нет страниц
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="col-md-9">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0" style="border-radius: 12px; min-height: 600px;">
                @if($selectedPage)
                    <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                        <h1 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0;">{{ $selectedPage->title }}</h1>
                        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'redactor')
                            <div class="btn-group">
                                <a href="{{ route('knowledge-base.edit', $selectedPage->id) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-pencil me-1"></i> Редактировать
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDeleteKB({{ $selectedPage->id }})">
                                    <i class="bi bi-trash me-1"></i> Удалить
                                </button>
                                <form id="delete-kb-form-{{ $selectedPage->id }}" action="{{ route('knowledge-base.destroy', $selectedPage->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        @endif
                    </div>
                    <div class="card-body px-4 pb-5">
                        <div class="text-muted mb-4" style="font-size: 0.9rem;">
                            <i class="bi bi-person me-1"></i> {{ $selectedPage->user->name ?? 'Автор' }} | 
                            <i class="bi bi-clock me-1"></i> {{ $selectedPage->updated_at->format('d.m.Y H:i') }}
                        </div>
                        <div class="kb-content" style="line-height: 1.6; font-size: 1.1rem; color: #333;">
                            {!! nl2br(e($selectedPage->content)) !!}
                        </div>
                    </div>
                @else
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-muted">
                        <i class="bi bi-book mb-3" style="font-size: 3rem;"></i>
                        <h5>Выберите страницу или создайте новую</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDeleteKB(id) {
        if (confirm('Вы уверены, что хотите удалить эту страницу?')) {
            document.getElementById('delete-kb-form-' + id).submit();
        }
    }
</script>

<style>
    .kb-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 20px 0;
    }
    .list-group-item.active {
        font-weight: 600;
    }
</style>
@endsection
