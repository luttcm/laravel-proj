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
                        <img src="{{ asset($item->firstPicture->path) }}" class="news-image js-picture-open" data-image="{{ asset($item->firstPicture->path) }}">
                    </div>
                @endif

                <div class="news-card-header">
                    <h3 class="news-title">{{ $item->title }}</h3>
                </div>

                <div class="news-card-text">
                    <p class="news-text">
                        @if(strlen(strip_tags($item->content)) > 235)
                            {{ Illuminate\Support\Str::limit(strip_tags($item->content), 235, '') }}
                            <br> <b class="continue-reading">Читать далее...</b>
                        @else
                            {{ strip_tags($item->content) }}
                        @endif
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

<div class="modal fade" id="pictureModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content" style="background-color: rgba(0,0,0,0.9);">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between;">
                <div style="color: #fff; font-size: 0.9rem;"><span id="pictureCounter">1</span> / <span id="pictureTotal">1</span></div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center" style="min-height: 90vh; position: relative;">
                <button id="prevPictureBtn" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.2); border: none; color: white; width: 50px; height: 50px; border-radius: 50%; font-size: 24px; cursor: pointer; align-items: center; justify-content: center; transition: all 0.3s ease;" class="d-flex" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    ‹
                </button>

                <img id="pictureModalImage" src="" alt="" style="max-width: 100%; max-height: 100%; object-fit: contain;">

                <button id="nextPictureBtn" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.2); border: none; color: white; width: 50px; height: 50px; border-radius: 50%; font-size: 24px; cursor: pointer; align-items: center; justify-content: center; transition: all 0.3s ease;" class="d-flex" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    ›
                </button>
            </div>
        </div>
    </div>
</div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const newsModal = new bootstrap.Modal(document.getElementById('newsModal'));
        const pictureModal = new bootstrap.Modal(document.getElementById('pictureModal'));
        
        let currentPictureIndex = 0;
        let currentPictures = [];

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

            const pictureWrapper = e.target.closest('.js-picture-open');
            if (pictureWrapper) {
                e.stopPropagation();
                e.preventDefault();

                currentPictures = Array.from(document.querySelectorAll('.js-picture-open'))
                    .map(pic => pic.dataset.image);
                
                const clickedSrc = pictureWrapper.dataset.image;
                currentPictureIndex = currentPictures.indexOf(clickedSrc);
                
                document.getElementById('pictureModalImage').src = clickedSrc;
                
                document.getElementById('pictureCounter').textContent = currentPictureIndex + 1;
                document.getElementById('pictureTotal').textContent = currentPictures.length;
                
                updatePictureNavButtons();
                
                pictureModal.show();
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
                            <div class="row g-3">
                                ${data.pictures.map(pic => `
                                    <div class="col-md-4">
                                        <div style="border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.08); transition: all 0.2s; cursor: pointer;" class="news-picture-wrapper js-picture-open" data-image="${pic.path}">
                                            <img src="${pic.path}" class="img-fluid" style="height:150px; width:100%; object-fit:cover; display:block;" alt="">
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                    <div class="mt-3">${data.content}</div>

                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex gap-2 mb-3">
                            <button class="btn btn-sm js-modal-like" data-news-id="${newsId}">
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
                        commentStatus.textContent = 'Комментарий добавлен';
                        setTimeout(() => { commentStatus.textContent = ''; }, 2000);

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
                        commentStatus.textContent = 'Ошибка: ' + err.message;
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

        function updatePictureNavButtons() {
            const prevBtn = document.getElementById('prevPictureBtn');
            const nextBtn = document.getElementById('nextPictureBtn');

            prevBtn.style.opacity = currentPictureIndex === 0 ? '0.3' : '0.7';
            prevBtn.style.pointerEvents = currentPictureIndex === 0 ? 'none' : 'auto';
            
            nextBtn.style.opacity = currentPictureIndex === currentPictures.length - 1 ? '0.3' : '0.7';
            nextBtn.style.pointerEvents = currentPictureIndex === currentPictures.length - 1 ? 'none' : 'auto';
        }

        function showPictureByIndex(index) {
            if (index >= 0 && index < currentPictures.length) {
                currentPictureIndex = index;
                document.getElementById('pictureModalImage').src = currentPictures[index];
                document.getElementById('pictureCounter').textContent = currentPictureIndex + 1;
                updatePictureNavButtons();
            }
        }

        document.getElementById('prevPictureBtn').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (currentPictureIndex > 0) {
                showPictureByIndex(currentPictureIndex - 1);
            }
        });

        document.getElementById('nextPictureBtn').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (currentPictureIndex < currentPictures.length - 1) {
                showPictureByIndex(currentPictureIndex + 1);
            }
        });

        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('pictureModal');
            if (modal && modal.classList.contains('show')) {
                if (e.key === 'ArrowLeft') {
                    if (currentPictureIndex > 0) {
                        showPictureByIndex(currentPictureIndex - 1);
                    }
                } else if (e.key === 'ArrowRight') {
                    if (currentPictureIndex < currentPictures.length - 1) {
                        showPictureByIndex(currentPictureIndex + 1);
                    }
                }
            }
        });
    });

    <style>
        .news-picture-wrapper {
            cursor: pointer;
        }

        .news-picture-wrapper:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        .news-picture-wrapper img {
            transition: transform 0.2s;
        }

        .news-picture-wrapper:hover img {
            transform: scale(1.02);
        }
    </style>

    @endsection
