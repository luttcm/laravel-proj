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
    /** @var NewsService */
    protected $newsService;
    /** @var CommentService */
    protected $commentService;

    public function __construct(NewsService $newsService, CommentService $commentService)
    {
        $this->newsService = $newsService;
        $this->commentService = $commentService;
    }

    /**
     * @OA\Get(
     *     path="/news",
     *     summary="Список новостей",
     *     tags={"News"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Список новостей (HTML или JSON)")
     * )
     */
    public function index(): \Illuminate\View\View
    {
        $news = $this->newsService->getAllNews();
        return view('pages.news.index', compact('news'));
    }

    /**
     * @OA\Get(
     *     path="/news/{id}",
     *     summary="Детали новости",
     *     tags={"News"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Данные новости"),
     *     @OA\Response(response=404, description="Новость не найдена")
     * )
     */
    public function show(int $id): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\View\View
    {
        $details = $this->newsService->getNewsDetails($id);
        $newsItem = $details['newsItem'];
        if (!$newsItem) {
            abort(404);
        }
        $pictures = $details['pictures'];

        if (request()->wantsJson() || request()->expectsJson() || request()->ajax()) {
            return response()->json($this->newsService->formatNewsShowResponse($newsItem, $pictures, auth()->user()));
        }

        return view('pages.news.detail', compact('newsItem', 'pictures'));
    }

    public function create(): \Illuminate\View\View
    {
        return view('pages.news.create');
    }

    /**
     * @OA\Post(
     *     path="/news",
     *     summary="Создать новость",
     *     tags={"News"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title", "content"},
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="images[]", type="array", @OA\Items(type="string", format="binary"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=302, description="Перенаправление после создания"),
     *     @OA\Response(response=422, description="Ошибка валидации")
     * )
     */
    public function store(StoreNewsRequest $request): \Illuminate\Http\RedirectResponse
    {
        $result = $this->newsService->createNews(
            $request->validated(), 
            $request->file('images'), 
            (int)auth()->id()
        );

        return redirect()->route('news.index')->with('success', $result['message']);
    }

    public function edit(int $id): \Illuminate\View\View
    {
        $details = $this->newsService->getNewsDetails($id);
        $news = $details['newsItem'];
        if (!$news) {
            abort(404);
        }
        $pictures = $details['pictures'];
        return view('pages.news.edit', compact('news', 'pictures'));
    }

    /**
     * @OA\Put(
     *     path="/news/{id}",
     *     summary="Обновить новость",
     *     tags={"News"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title", "content"},
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="images[]", type="array", @OA\Items(type="string", format="binary"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=302, description="Перенаправление после обновления"),
     *     @OA\Response(response=404, description="Новость не найдена")
     * )
     */
    public function update(UpdateNewsRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $message = $this->newsService->updateNews(
            $id,
            $request->validated(),
            $request->file('images')
        );

        return redirect()->route('news.show', ['id' => $id])->with('success', $message);
    }

    /**
     * @OA\Delete(
     *     path="/news/{id}",
     *     summary="Удалить новость",
     *     tags={"News"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Успешное удаление (JSON)"),
     *     @OA\Response(response=302, description="Перенаправление после удаления")
     * )
     */
    public function destroy(int $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $this->newsService->deleteNews($id);

        if (request()->wantsJson() || request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Новость удалена']);
        }

        return redirect()->route('news.index')->with('success', 'Новость удалена');
    }

    /**
     * @OA\Post(
     *     path="/news/{id}/like",
     *     summary="Лайкнуть новость",
     *     tags={"News"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Результат лайка (JSON)"),
     *     @OA\Response(response=302, description="Перенаправление")
     * )
     */
    public function toggleLike(Request $request, int $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        /** @var array<int, int> $liked */
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

    /**
     * @OA\Post(
     *     path="/news/{newsId}/comments",
     *     summary="Добавить комментарий",
     *     tags={"News"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="newsId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(property="content", type="string", example="Отличная новость!")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Комментарий добавлен (JSON)"),
     *     @OA\Response(response=302, description="Перенаправление")
     * )
     */
    public function storeComment(StoreCommentRequest $request, int $newsId): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $comment = $this->commentService->createComment(
            $request->validated(),
            $newsId,
            (int)auth()->id()
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
                'created_at' => $comment->created_at ? $comment->created_at->format('Y-m-d H:i') : '',
                'canDelete' => true,
            ]);
        }

        return redirect()->back();
    }

    /**
     * @OA\Delete(
     *     path="/pictures/{pictureId}",
     *     summary="Удалить картинку новости",
     *     tags={"News"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="pictureId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Успешное удаление (JSON)"),
     *     @OA\Response(response=302, description="Перенаправление")
     * )
     */
    public function deletePicture(int $pictureId): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        if (!$user || !$this->newsService->canUserManagePicture($user, $pictureId)) {
            abort(403);
        }

        $this->newsService->deletePicture($pictureId);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Картинка удалена');
    }

    /**
     * @OA\Delete(
     *     path="/news/{newsId}/comments/{commentId}",
     *     summary="Удалить комментарий",
     *     tags={"News"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="newsId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="commentId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Успешное удаление (JSON)"),
     *     @OA\Response(response=302, description="Перенаправление")
     * )
     */
    public function deleteComment(int $newsId, int $commentId): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $comment = app(\App\Repositories\CommentRepository::class)->findById($commentId);
        if (!$comment) {
            abort(404);
        }

        if ($comment->news_id != $newsId) {
            abort(404);
        }
        
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }
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

