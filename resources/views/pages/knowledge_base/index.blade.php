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
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="kbAddMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="kbAddMenu">
                                <li><a class="dropdown-item" href="{{ route('knowledge-base.create') }}">Добавить страницу</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#createCategoryModal">Добавить раздел</a></li>
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="accordion accordion-flush" id="kbCategoriesAccordion">
                    @forelse($categories as $category)
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header" id="heading-{{ $category->id }}">
                                <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $category->id }}" aria-expanded="false" aria-controls="collapse-{{ $category->id }}" style="background-color: transparent; box-shadow: none; font-weight: 600;">
                                    {{ $category->title }}
                                </button>
                            </h2>
                            <div id="collapse-{{ $category->id }}" class="accordion-collapse collapse {{ ($selectedPage && $selectedPage->category_id === $category->id) ? 'show' : '' }}" aria-labelledby="heading-{{ $category->id }}" data-bs-parent="#kbCategoriesAccordion">
                                <div class="accordion-body p-0">
                                    <div class="list-group list-group-flush">
                                        @foreach($category->pages as $page)
                                            <a href="{{ route('knowledge-base.index', ['page' => $page->id]) }}" 
                                            class="list-group-item list-group-item-action border-0 py-2 ps-4 {{ $selectedPage && $selectedPage->id === $page->id ? 'active' : '' }}"
                                            style="{{ $selectedPage && $selectedPage->id === $page->id ? 'background-color: #f0f7ff; color: #0d6efd; border-left: 4px solid #0d6efd !important;' : 'border-left: 4px solid transparent !important;' }}">
                                                {{ $page->title }}
                                            </a>
                                        @endforeach
                                    </div>
                                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'redactor')
                                        <div class="d-flex justify-content-end px-3 py-2 bg-light">
                                            <button class="btn btn-sm btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#editCategoryModal-{{ $category->id }}"><i class="bi bi-pencil"></i></button>
                                            <form action="{{ route('knowledge-base.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Удалить раздел? Все страницы в нем останутся без раздела.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Edit Category Modal -->
                        <div class="modal fade" id="editCategoryModal-{{ $category->id }}" tabindex="-1" aria-labelledby="editCategoryModalLabel-{{ $category->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('knowledge-base.categories.update', $category->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editCategoryModalLabel-{{ $category->id }}">Редактировать раздел</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Название</label>
                                                <input type="text" name="title" class="form-control" value="{{ $category->title }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Порядок</label>
                                                <input type="number" name="order" class="form-control" value="{{ $category->order }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                            <button type="submit" class="btn btn-primary">Сохранить</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            Нет разделов
                        </div>
                    @endforelse
                    
                    @php
                        $uncategorizedPages = $pages->where('category_id', null);
                    @endphp
                    @if($uncategorizedPages->count() > 0)
                         <div class="accordion-item border-0 mt-2">
                            <h2 class="accordion-header" id="heading-uncategorized">
                                <button class="accordion-button py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-uncategorized" aria-expanded="true" aria-controls="collapse-uncategorized" style="background-color: transparent; box-shadow: none; font-weight: 600; color: #6c757d;">
                                    Без раздела
                                </button>
                            </h2>
                            <div id="collapse-uncategorized" class="accordion-collapse collapse show" aria-labelledby="heading-uncategorized" data-bs-parent="#kbCategoriesAccordion">
                                <div class="accordion-body p-0">
                                    <div class="list-group list-group-flush">
                                        @foreach($uncategorizedPages as $page)
                                            <a href="{{ route('knowledge-base.index', ['page' => $page->id]) }}" 
                                            class="list-group-item list-group-item-action border-0 py-2 ps-4 {{ $selectedPage && $selectedPage->id === $page->id ? 'active' : '' }}"
                                            style="{{ $selectedPage && $selectedPage->id === $page->id ? 'background-color: #f0f7ff; color: #0d6efd; border-left: 4px solid #0d6efd !important;' : 'border-left: 4px solid transparent !important;' }}">
                                                {{ $page->title }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0" style="border-radius: 12px; min-height: 600px;">
                @if($selectedPage)
                    <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            @if($selectedPage->category)
                                <span class="badge bg-secondary mb-2">{{ $selectedPage->category->title }}</span>
                            @endif
                            <h1 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0;">{{ $selectedPage->title }}</h1>
                        </div>
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
                        
                        @if($selectedPage->photo_path)
                            <div class="mb-4 text-center">
                                <img src="{{ asset('storage/' . $selectedPage->photo_path) }}" alt="{{ $selectedPage->title }}" class="img-fluid rounded shadow-sm" style="max-height: 400px; object-fit: contain;">
                            </div>
                        @endif

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

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('knowledge-base.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createCategoryModalLabel">Создать раздел</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Название раздела</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Порядок отображения</label>
                        <input type="number" name="order" class="form-control" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать</button>
                </div>
            </form>
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
