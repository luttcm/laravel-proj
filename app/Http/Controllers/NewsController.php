<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Services\NewsService;
use App\Services\CommentService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    protected $newsService;
    protected $commentService;

    public function __construct(NewsService $newsService, CommentService $commentService)
    {
        $this->newsService = $newsService;
        $this->commentService = $commentService;
    }

    public function index()
    {
        $news = $this->newsService->getAllNews();
        return view('pages.news.index', compact('news'));
    }

    public function show($id)
    {
        $details = $this->newsService->getNewsDetails($id);
        $newsItem = $details['newsItem'];
        $pictures = $details['pictures'];
        
        $comments = app(\App\Repositories\CommentRepository::class)->getByNewsId($id);

        $reactions = (int)($newsItem->reactions ?? 0);

        if (request()->wantsJson() || request()->expectsJson() || request()->ajax()) {
            $user = auth()->user();
            $canEdit = $user && in_array($user->role, ['admin', 'manager']);
            $canDelete = $user && in_array($user->role, ['admin', 'redactor', 'manager']);

            return response()->json([
                'id' => $newsItem->id,
                'title' => $newsItem->title,
                'content' => $newsItem->content,
                'reactions' => $reactions,
                'author' => $newsItem->author ? [
                    'name' => $newsItem->author->name,
                    'role' => $newsItem->author->role,
                ] : null,
                'created_at' => $newsItem->created_at,
                'pictures' => $pictures->map(fn($p) => ['path' => asset($p->path)])->all(),
                'comments' => $comments->map(fn(\App\Models\Comment $c) => [
                    'id' => $c->id,
                    'content' => $c->content,
                    'user' => [
                        'id' => $c->user->id,
                        'name' => $c->user->name,
                    ],
                    'created_at' => $c->created_at->format('Y-m-d H:i'),
                    'canDelete' => $user && ($user->id === $c->user_id || in_array($user->role, ['admin', 'redactor'])),
                ])->all(),
                'canEdit' => $canEdit,
                'canDelete' => $canDelete,
            ]);
        }

        return view('pages.news.detail', compact('newsItem', 'pictures'));
    }

    public function create()
    {
        return view('pages.news.create');
    }

    public function store(StoreNewsRequest $request)
    {
        $result = $this->newsService->createNews(
            $request->validated(), 
            $request->file('images'), 
            auth()->id()
        );

        return redirect()->route('news.index')->with('success', $result['message']);
    }

    public function edit($id)
    {
        $details = $this->newsService->getNewsDetails($id);
        $news = $details['newsItem'];
        $pictures = $details['pictures'];
        return view('pages.news.edit', compact('news', 'pictures'));
    }

    public function update(UpdateNewsRequest $request, $id)
    {
        $message = $this->newsService->updateNews(
            $id,
            $request->validated(),
            $request->file('images')
        );

        return redirect()->route('news.show', ['id' => $id])->with('success', $message);
    }

    public function destroy($id)
    {
        $this->newsService->deleteNews($id);

        if (request()->wantsJson() || request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Новость удалена']);
        }

        return redirect()->route('news.index')->with('success', 'Новость удалена');
    }

    public function toggleLike(Request $request, $id)
    {
        $liked = session('liked_news', []);
        $liked = array_map('intval', $liked);

        $result = $this->newsService->toggleLike((int)$id, $liked);
        
        session(['liked_news' => $result['liked_news']]);

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json([
                'reactions' => $result['reactions'],
                'liked' => $result['liked'],
            ]);
        }

        return redirect()->back();
    }

    public function storeComment(StoreCommentRequest $request, $newsId)
    {
        $comment = $this->commentService->createComment(
            $request->validated(),
            $newsId,
            auth()->id()
        );

        if ($request->expectsJson()) {
            $comment->load('user');
            return response()->json([
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                ],
                'created_at' => $comment->created_at->format('Y-m-d H:i'),
                'canDelete' => true,
            ]);
        }

        return redirect()->back();
    }

    public function deletePicture($pictureId)
    {
        $picture = app(\App\Repositories\PictureRepository::class)->findById($pictureId);
        
        if ($picture->entity_type !== 'news') {
            abort(403);
        }

        $user = auth()->user();
        $news = app(\App\Repositories\NewsRepository::class)->findById($picture->entity_id);
        
        if ($user->id !== $news->author_id && !in_array($user->role, ['admin', 'redactor'])) {
            abort(403);
        }

        $this->newsService->deletePicture($pictureId);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Картинка удалена');
    }

    public function deleteComment($newsId, $commentId)
    {
        $comment = app(\App\Repositories\CommentRepository::class)->findById($commentId);

        if ($comment->news_id != $newsId) {
            abort(404);
        }
        
        $user = auth()->user();
        if ($user->id !== $comment->user_id && !in_array($user->role, ['admin', 'redactor'])) {
            abort(403);
        }

        $this->commentService->deleteComment($commentId);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }
}

