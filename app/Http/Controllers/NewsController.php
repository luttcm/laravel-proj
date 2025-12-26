<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Picture;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::orderBy('created_at', 'desc')->get();

        foreach ($news as $n) {
            $n->firstPicture = Picture::where('entity_type', 'news')
                ->where('entity_id', $n->id)
                ->first();
        }

        return view('pages.news', compact('news'));
    }

    public function show($id)
    {
        $newsItem = News::findOrFail($id);
        $pictures = Picture::where('entity_type', 'news')->where('entity_id', $id)->get();
        $newsItem->load('author');

        if (request()->wantsJson() || request()->expectsJson() || request()->ajax()) {
            $user = auth()->user();
            $canEdit = $user && in_array($user->role, ['admin', 'manager']);
            $canDelete = $user && in_array($user->role, ['admin', 'redactor', 'manager']);

            return response()->json([
                'id' => $newsItem->id,
                'title' => $newsItem->title,
                'content' => $newsItem->content,
                'author' => $newsItem->author ? [
                    'name' => $newsItem->author->name,
                    'role' => $newsItem->author->role,
                ] : null,
                'created_at' => $newsItem->created_at,
                'pictures' => $pictures->map(fn($p) => ['path' => asset($p->path)])->all(),
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
            'images.*' => 'nullable|image|max:4096',
        ]);

        $news = News::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'author_id' => auth()->id() ?? null,
            'reactions' => 0,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('news', 'public');
                Picture::create([
                    'path' => 'storage/' . $path,
                    'entity_type' => 'news',
                    'entity_id' => $news->id,
                ]);
            }
        }

        return redirect()->route('news.index')->with('success', 'Новость добавлена');
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
            'images.*' => 'nullable|image|max:4096',
        ]);

        $news = News::findOrFail($id);
        $news->title = $validated['title'];
        $news->content = $validated['content'];
        $news->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('news', 'public');
                Picture::create([
                    'path' => 'storage/' . $path,
                    'entity_type' => 'news',
                    'entity_id' => $news->id,
                ]);
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
}
