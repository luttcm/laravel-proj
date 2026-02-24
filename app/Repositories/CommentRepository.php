<?php

namespace App\Repositories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

class CommentRepository
{
    public function getByNewsId(int $newsId): Collection
    {
        return Comment::where('news_id', $newsId)->with('user')->latest()->get();
    }

    public function create(array $data): Comment
    {
        return Comment::create($data);
    }

    public function delete(int $id): bool
    {
        return Comment::findOrFail($id)->delete();
    }
    
    public function findById(int $id): ?Comment
    {
        return Comment::findOrFail($id);
    }
}
