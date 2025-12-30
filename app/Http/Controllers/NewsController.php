<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Picture;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with('comments')->orderBy('created_at', 'desc')->get();

        foreach ($news as $n) {
            $n->pictures = Picture::where('entity_type', 'news')
                ->where('entity_id', $n->id)
                ->limit(9)
                ->get()
                ->map(fn($p) => asset($p->path));
            
            $n->firstPicture = $n->pictures->first();
            $n->comments_count = $n->comments->count();
        }

        return view('pages.news', compact('news'));
    }

    public function show($id)
    {
        $newsItem = News::findOrFail($id);
        $pictures = Picture::where('entity_type', 'news')
            ->where('entity_id', $id)
            ->limit(9)
            ->get();
        $newsItem->load('author');
        $comments = Comment::where('news_id', $id)->with('user')->latest()->get();

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
                'comments' => $comments->map(fn($c) => [
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

        return view('pages.news-detail', compact('newsItem', 'pictures'));
    }

    public function create()
    {
        return view('pages.news-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'images' => 'nullable|array|max:9',
            'images.*' => 'nullable|image|max:4096',
        ], [
            'images.max' => 'Максимум 9 картинок в новости',
        ]);

        $news = News::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'author_id' => auth()->id() ?? null,
            'reactions' => 0,
        ]);

        $message = 'Новость добавлена';
        if ($request->hasFile('images')) {
            $imageCount = 0;
            $skipped = 0;
            foreach ($request->file('images') as $file) {
                if ($imageCount >= 9) {
                    $skipped++;
                    continue;
                }
                $path = $file->store('news', 'public');
                Picture::create([
                    'path' => 'storage/' . $path,
                    'entity_type' => 'news',
                    'entity_id' => $news->id,
                ]);
                $imageCount++;
            }
            if ($skipped > 0) {
                $message .= " ({$skipped} картинок пропущено - максимум 9)";
            }
        }

        return redirect()->route('news.index')->with('success', $message);
    }

    public function edit($id)
    {
        $news = News::findOrFail($id);
        $pictures = Picture::where('entity_type', 'news')->where('entity_id', $id)->get();
        return view('pages.news-edit', compact('news', 'pictures'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'images' => 'nullable|array|max:9',
            'images.*' => 'nullable|image|max:4096',
        ], [
            'images.max' => 'Максимум 9 картинок в новости',
        ]);

        $news = News::findOrFail($id);
        $news->title = $validated['title'];
        $news->content = $validated['content'];
        $news->save();

        if ($request->hasFile('images')) {
            $existingCount = Picture::where('entity_type', 'news')->where('entity_id', $news->id)->count();
            $canAdd = 9 - $existingCount;

            $imageCount = 0;
            foreach ($request->file('images') as $file) {
                if ($imageCount >= $canAdd) break;
                $path = $file->store('news', 'public');
                Picture::create([
                    'path' => 'storage/' . $path,
                    'entity_type' => 'news',
                    'entity_id' => $news->id,
                ]);
                $imageCount++;
            }
        }

        return redirect()->route('news.show', ['id' => $news->id])->with('success', 'Новость обновлена');
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);
        Picture::where('entity_type', 'news')->where('entity_id', $id)->delete();
        $news->delete();

        if (request()->wantsJson() || request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Новость удалена']);
        }

        return redirect()->route('news.index')->with('success', 'Новость удалена');
    }

    public function toggleLike(Request $request, $id)
    {
        $news = News::findOrFail($id);
        $liked = session('liked_news', []);

        $idInt = (int) $id;
        $liked = array_map('intval', $liked);

        if (in_array($idInt, $liked)) {
            $news->decrement('reactions');
            $liked = array_values(array_diff($liked, [$idInt]));
            $isLiked = false;
        } else {
            $news->increment('reactions');
            $liked[] = $idInt;
            $isLiked = true;
        }

        session(['liked_news' => $liked]);

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json([
                'reactions' => $news->reactions,
                'liked' => $isLiked,
            ]);
        }

        return redirect()->back();
    }

    public function storeComment(Request $request, $newsId)
    {
        $news = News::findOrFail($newsId);
        
        try {
            $validated = $request->validate([
                'content' => 'required|string|max:500',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            throw $e;
        }

        $comment = Comment::create([
            'news_id' => $newsId,
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        if ($request->expectsJson()) {
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
        $picture = Picture::findOrFail($pictureId);
        
        // Проверяем что это картинка новости
        if ($picture->entity_type !== 'news') {
            abort(403);
        }

        $user = auth()->user();
        $news = News::findOrFail($picture->entity_id);
        
        // Только автор, админ или редактор могут удалять картинки
        if ($user->id !== $news->author_id && !in_array($user->role, ['admin', 'redactor'])) {
            abort(403);
        }

        // Удаляем файл если нужно
        if (Storage::disk('public')->exists(str_replace('storage/', '', $picture->path))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $picture->path));
        }

        $picture->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Картинка удалена');
    }

    public function deleteComment($newsId, $commentId)
    {
        $comment = Comment::findOrFail($commentId);

        if ($comment->news_id != $newsId) {
            abort(404);
        }
        $user = auth()->user();
        if ($user->id !== $comment->user_id && !in_array($user->role, ['admin', 'redactor'])) {
            abort(403);
        }

        $comment->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }
}
