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
                    <button class="btn js-like-button" data-url="{{ route('news.like', ['id' => $item->id]) }}" data-id="{{ $item->id }}" style="flex:1; max-width: 30px;">
                        <img src="{{ asset('img/like.png') }}" alt="Logo" style="height:15px; width: 15px;">
                        <span class="js-like-count">{{ $item->reactions }}</span>
                    </button>
                    <button class="btn">
                        <img src="{{ asset('img/comment.png') }}" style="height:15px; width: 15px;">    
                        <span class="js-comments-count">{{ $item->comments_count ?? 0 }}</span>
                    </button>

                    @if(auth()->user() && in_array(auth()->user()->role, ['admin','redactor']))
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
                
                let bodyHtml = `
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

                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex gap-2 mb-3">
                            <button class="btn btn-sm btn-outline-secondary js-modal-like" data-news-id="${newsId}">
                                <img src="{{ asset('img/like.png') }}" alt="Like" style="height:15px; width:15px;">
                                <span class="js-modal-like-count">${data.reactions || 0}</span>
                            </button>
                            <span class="text-muted"><img src="{{ asset('img/comment.png') }}" style="height:15px; width:15px;"> ${data.comments?.length || 0}</span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h6>Комментарии</h6>
                        <div id="commentsList" class="mb-3" style="max-height:300px; overflow-y:auto;">
                            ${!data.comments || data.comments.length === 0 ? '<p class="text-muted small">Нет комментариев</p>' : ''}
                            ${data.comments ? data.comments.map(c => `
                                <div class="comment-item p-2 border-bottom" data-comment-id="${c.id}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>${c.user.name}</strong>
                                            <span class="text-muted small">${c.created_at}</span>
                                        </div>
                                        ${c.canDelete ? `<button class="btn btn-sm btn-outline-danger js-delete-comment" data-comment-id="${c.id}" style="padding:2px 6px; font-size:11px;">✕</button>` : ''}
                                    </div>
                                    <p class="mt-1 mb-0 small">${c.content}</p>
                                </div>
                            `).join('') : ''}
                        </div>
                        <div class="mt-2">
                            <form id="commentForm" data-news-id="${data.id}">
                                <div class="input-group input-group-sm">
                                    <input type="text" id="commentInput" class="form-control" placeholder="Добавить комментарий..." maxlength="500">
                                    <button class="btn btn-primary" type="submit">Отправить</button>
                                </div>
                                <div class="text-muted small mt-1" id="commentStatus"></div>
                            </form>
                        </div>
                    </div>
                `;

                modalBody.innerHTML = bodyHtml;
                modalFooter.innerHTML = '';
                newsModal.show();

                // Обработчик лайка в попапе
                const modalLikeBtn = document.querySelector('.js-modal-like');
                if (modalLikeBtn) {
                    modalLikeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const newsIdFromBtn = this.dataset.newsId;
                        const url = `/news/${newsIdFromBtn}/like`;
                        const countEl = this.querySelector('.js-modal-like-count');
                        this.disabled = true;

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
                                if (data.liked) this.classList.add('liked'); 
                                else this.classList.remove('liked');
                            }
                        })
                        .catch(err => console.error(err))
                        .finally(() => this.disabled = false);
                    });
                }

                // Обработчик удаления комментариев
                document.querySelectorAll('.js-delete-comment').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const commentId = this.dataset.commentId;
                        if (!confirm('Удалить комментарий?')) return;

                        fetch(`/news/${data.id}/comments/${commentId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        })
                        .then(res => res.json())
                        .then(() => {
                            const elem = document.querySelector(`[data-comment-id="${commentId}"]`);
                            if (elem) {
                                elem.style.opacity = '0.5';
                                setTimeout(() => elem.remove(), 200);
                            }
                        })
                        .catch(err => console.error(err));
                    });
                });

                // Обработчик формы комментариев
                const commentForm = document.getElementById('commentForm');
                const commentInput = document.getElementById('commentInput');
                const commentStatus = document.getElementById('commentStatus');
                
                commentForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const content = commentInput.value.trim();
                    if (!content) return;

                    const btn = this.querySelector('button[type="submit"]');
                    btn.disabled = true;
                    commentStatus.textContent = 'Отправка...';

                    fetch(`/news/${data.id}/comments`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({ content })
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Ошибка добавления');
                        return res.json();
                    })
                    .then(comment => {
                        const commentsList = document.getElementById('commentsList');
                        const noCommentsMsg = commentsList.querySelector('.text-muted');
                        if (noCommentsMsg) noCommentsMsg.remove();

                        const newCommentHtml = `
                            <div class="comment-item p-2 border-bottom" data-comment-id="${comment.id}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>${comment.user.name}</strong>
                                        <span class="text-muted small">${comment.created_at}</span>
                                    </div>
                                    ${comment.canDelete ? `<button class="btn btn-sm btn-outline-danger js-delete-comment" data-comment-id="${comment.id}" style="padding:2px 6px; font-size:11px;">✕</button>` : ''}
                                </div>
                                <p class="mt-1 mb-0 small">${comment.content}</p>
                            </div>
                        `;
                        
                        const firstComment = commentsList.querySelector('.comment-item');
                        if (firstComment) {
                            firstComment.insertAdjacentHTML('beforebegin', newCommentHtml);
                        } else {
                            commentsList.innerHTML = newCommentHtml;
                        }

                        commentInput.value = '';
                        commentStatus.textContent = '✓ Комментарий добавлен';
                        setTimeout(() => { commentStatus.textContent = ''; }, 2000);

                        // Переприсвоить обработчик удаления для нового комментария
                        const newDeleteBtn = commentsList.querySelector(`[data-comment-id="${comment.id}"] .js-delete-comment`);
                        if (newDeleteBtn) {
                            newDeleteBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                const cId = this.dataset.commentId;
                                if (!confirm('Удалить комментарий?')) return;

                                fetch(`/news/${data.id}/comments/${cId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': csrf,
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    credentials: 'same-origin'
                                })
                                .then(res => res.json())
                                .then(() => {
                                    const elem = document.querySelector(`[data-comment-id="${cId}"]`);
                                    if (elem) {
                                        elem.style.opacity = '0.5';
                                        setTimeout(() => elem.remove(), 200);
                                    }
                                })
                                .catch(err => console.error(err));
                            });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        commentStatus.textContent = '✗ Ошибка: ' + err.message;
                        setTimeout(() => { commentStatus.textContent = ''; }, 3000);
                    })
                    .finally(() => {
                        btn.disabled = false;
                    });
                });
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
