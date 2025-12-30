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
                    <div class="d-flex gap-2 flex-wrap" id="picturesContainer">
                        @foreach($pictures as $p)
                            <div class="picture-item" data-picture-id="{{ $p->id }}" style="position: relative; display: inline-block;">
                                <img src="{{ asset($p->path) }}" style="width:120px;height:120px;object-fit:cover;border-radius:6px;">
                                <button type="button" class="btn btn-sm btn-danger js-delete-picture" data-id="{{ $p->id }}" style="position: absolute; top: 2px; right: 2px; padding: 2px 6px; font-size: 12px;">✕</button>
                            </div>
                        @endforeach
                    </div>
                </div>
                <button class="btn btn-primary">Сохранить</button>
            </form>
        </div>
    </div>
</div>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.js-delete-picture').forEach(btn => {
        btn.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            
            const pictureId = this.dataset.id;
            const pictureItem = this.closest('.picture-item');
            
            console.log('Удаляем картинку:', pictureId);
            
            if (!confirm('Удалить картинку?')) {
                return false;
            }
            
            pictureItem.remove();
            console.log('Картинка удалена из DOM');

            fetch(`/pictures/${pictureId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
            .catch(err => {
                console.error('Ошибка удаления на сервере:', err);
            });
            
            return false;
        }, true);
    });

    const imageInput = document.querySelector('input[name="images[]"]');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            const fileCount = this.files.length;
            const existingCount = document.querySelectorAll('.picture-item').length;
            const total = fileCount + existingCount;
            
            console.log(`Существующих: ${existingCount}, Новых: ${fileCount}, Всего: ${total}`);
            
            if (total > 9) {
                alert(`Максимум 9 картинок!\n\n Можно добавить только ${9 - existingCount} картинок.`);

                if (existingCount >= 9) {
                    this.value = '';
                    return;
                }
            }
        });
    }
});
</script>
@endsection
