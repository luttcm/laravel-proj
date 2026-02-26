<?php

namespace App\Repositories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

class CommentRepository
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment>
     */
    public function getByNewsId(int $newsId): Collection
    {
        return Comment::where('news_id', $newsId)->with('user')->latest()->get();
    }

    /**
     * @param array<string, mixed> $data
     * @return Comment
     */
    public function create(array $data): Comment
    {
        return Comment::create($data);
    }

    public function delete(int $id): bool
    {
        return (bool)Comment::findOrFail($id)->delete();
    }
    
    public function findById(int $id): ?Comment
    {
        return Comment::findOrFail($id);
    }
}
