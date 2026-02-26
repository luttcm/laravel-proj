<?php

namespace App\Repositories;

use App\Models\News;
use Illuminate\Database\Eloquent\Collection;

class NewsRepository
{
    /**
     * @return Collection<int, News>
     */
    public function getAllWithComments(): Collection
    {
        return News::with('comments')->orderBy('created_at', 'desc')->get();
    }

    public function findById(int $id): ?News
    {
        /** @var News|null $news */
        $news = News::with('author')->find($id);
        return $news;
    }

    /**
     * @param array<string, mixed> $data
     * @return News
     */
    public function create(array $data): News
    {
        /** @var News $news */
        $news = News::create($data);
        return $news;
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $news = $this->findById($id);
        if (!$news) {
            return false;
        }
        return $news->update($data);
    }

    public function delete(int $id): bool
    {
        $news = $this->findById($id);
        if (!$news) {
            return false;
        }
        return (bool)$news->delete();
    }

    public function incrementReactions(int $id): bool
    {
        $news = $this->findById($id);
        if (!$news) {
            return false;
        }
        return (bool)$news->increment('reactions');
    }

    public function decrementReactions(int $id): bool
    {
        $news = $this->findById($id);
        if (!$news) {
            return false;
        }
        return (bool)$news->decrement('reactions');
    }
}
