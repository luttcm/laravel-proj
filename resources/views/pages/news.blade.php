@extends('layouts.app')

@section('title', 'Новости')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/news.css') }}">
@endpush
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
            <div class="news-card js-news-card" data-id="{{ $item->id }}" style="cursor: pointer;">
                
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

                    @if(auth()->user() && in_array(auth()->user()->role, ['admin','redactor','manager']))
                        <a href="{{ route('news.edit', ['id' => $item->id]) }}" class="btn js-prevent-modal" style="padding: 6px 4px;">
                            Редактировать
                        </a>
                        <button class="btn btn-sm btn-danger js-delete-card" data-id="{{ $item->id }}" style="padding: 6px 4px;">
                            Удалить
                        </button>
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

<div class="modal fade" id="newsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newsModalTitle">Новость</h5>
            </div>
            <div class="modal-body" id="newsModalBody">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="newsModalFooter">
            </div>
        </div>
    </div>
</div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const newsModal = new bootstrap.Modal(document.getElementById('newsModal'));

        document.addEventListener('click', function (e) {
            const newsCard = e.target.closest('.js-news-card');
            if (newsCard && !e.target.closest('button, a')) {
                const newsId = newsCard.dataset.id;
                loadNewsDetail(newsId, csrf, newsModal);
                return;
            }

            const likeBtn = e.target.closest('.js-like-button');
            if (likeBtn) {
                e.preventDefault();
                e.stopPropagation();

                const url = likeBtn.dataset.url;
                const countEl = likeBtn.querySelector('.js-like-count');
                likeBtn.disabled = true;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({})
                })
                .then(res => res.json())
                .then(data => {
                    if (data && typeof data.reactions !== 'undefined') {
                        countEl.textContent = data.reactions;
                        if (data.liked) likeBtn.classList.add('liked'); 
                        else likeBtn.classList.remove('liked');
                    }
                })
                .catch(err => console.error(err))
                .finally(() => likeBtn.disabled = false);
                return;
            }

            const deleteCardBtn = e.target.closest('.js-delete-card');
            if (deleteCardBtn) {
                e.preventDefault();
                e.stopPropagation();
                const newsId = deleteCardBtn.dataset.id;
                
                if (!confirm('Вы уверены, что хотите удалить эту новость?')) return;

                fetch(`/news/${newsId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(res => {
                    if (!res.ok) throw new Error('Delete failed');
                    return res.json();
                })
                .then(data => {
                    location.reload();
                })
                .catch(err => {
                    console.error(err);
                    alert('Ошибка удаления: ' + err.message);
                });
                return;
            }

            const deleteBtn = e.target.closest('.js-delete-news');
            if (deleteBtn) {
                e.preventDefault();
                e.stopPropagation();
                const newsId = deleteBtn.dataset.id;
                if (!confirm('Вы уверены, что хотите удалить эту новость?')) return;

                fetch(`/news/${newsId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(res => {
                    if (!res.ok) throw new Error('Delete failed');
                    return res.json();
                })
                .then(data => {
                    newsModal.hide();
                    location.reload();
                })
                .catch(err => {
                    console.error(err);
                    alert('Ошибка удаления: ' + err.message);
                });
                return;
            }
        });

        function loadNewsDetail(newsId, csrf, newsModal) {
            const modalBody = document.getElementById('newsModalBody');
            const modalTitle = document.getElementById('newsModalTitle');
            const modalFooter = document.getElementById('newsModalFooter');

            modalBody.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Загрузка...</span></div></div>';

            fetch(`/news/${newsId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(res => res.json())
            .then(data => {
                modalTitle.textContent = data.title;
                modalBody.innerHTML = `
                    <p class="text-muted small">
                        Автор: ${data.author ? data.author.name + ' (' + data.author.role + ')' : '—'}
                        • ${new Date(data.created_at).toLocaleString('ru-RU')}
                    </p>
                    ${data.pictures && data.pictures.length ? `
                        <div class="mb-3">
                            <div class="row g-2">
                                ${data.pictures.map(pic => `
                                    <div class="col-md-4">
                                        <img src="${pic.path}" class="img-fluid rounded" style="height:150px; object-fit:cover;" alt="">
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                    <div class="mt-3">${data.content}</div>
                `;

                let footerHtml = '';
                if (data.canEdit) {
                    footerHtml = `<a href="/news/${newsId}/edit" class="btn btn-primary">Редактировать</a>` + footerHtml;
                }
                if (data.canDelete) {
                    footerHtml = `<button type="button" class="btn btn-danger js-delete-news" data-id="${newsId}">Удалить</button>` + footerHtml;
                }
                modalFooter.innerHTML = footerHtml;
                newsModal.show();
            })
            .catch(err => {
                console.error(err);
                modalBody.innerHTML = '<div class="alert alert-danger">Ошибка загрузки новости</div>';
                newsModal.show();
            });
        }
    });
    </script>

    @endsection
