<?php

namespace App\Services;

use App\Repositories\CommentRepository;
use App\Models\Comment;

class CommentService
{
    /** @var CommentRepository */
    protected $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * @param array<string, mixed> $data
     * @param int $newsId
     * @param int $userId
     * @return Comment
     */
    public function createComment(array $data, int $newsId, int $userId): Comment
    {
        return $this->commentRepository->create([
            'news_id' => $newsId,
            'user_id' => $userId,
            'content' => $data['content'],
        ]);
    }

    public function deleteComment(int $commentId): bool
    {
        return $this->commentRepository->delete($commentId);
    }
}
