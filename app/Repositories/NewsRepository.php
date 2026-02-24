<?php

namespace App\Repositories;

use App\Models\News;
use Illuminate\Database\Eloquent\Collection;

class NewsRepository
{
    public function getAllWithComments(): Collection
    {
        return News::with('comments')->orderBy('created_at', 'desc')->get();
    }

    public function findById(int $id): ?News
    {
        return News::with('author')->findOrFail($id);
    }

    public function create(array $data): News
    {
        return News::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $news = $this->findById($id);
        return $news->update($data);
    }

    public function delete(int $id): bool
    {
        $news = $this->findById($id);
        return $news->delete();
    }

    public function incrementReactions(int $id): bool
    {
        $news = clone $this->findById($id);
        return $news->increment('reactions');
    }

    public function decrementReactions(int $id): bool
    {
        $news = clone $this->findById($id);
        return $news->decrement('reactions');
    }
}
