<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KnowledgeBasePage;
use Illuminate\Support\Facades\Auth;

class KnowledgeBaseController extends Controller
{
    public function index(Request $request)
    {
        $categories = \App\Models\KnowledgeBaseCategory::orderBy('order')->orderBy('title')->with('pages')->get();
        $pages = KnowledgeBasePage::orderBy('order')->orderBy('title')->get();
        $selectedPage = null;

        if ($request->has('page')) {
            $selectedPage = KnowledgeBasePage::find($request->page);
        } elseif ($pages->count() > 0) {
            $selectedPage = $pages->first();
        }

        return view('pages.knowledge_base.index', compact('categories', 'pages', 'selectedPage'));
    }

    public function create()
    {
        $categories = \App\Models\KnowledgeBaseCategory::orderBy('order')->orderBy('title')->get();
        return view('pages.knowledge_base.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'order' => 'nullable|integer',
            'category_id' => 'nullable|exists:knowledge_base_categories,id',
            'photo' => 'nullable|image|max:5120',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['order'] = $validated['order'] ?? 0;

        $validated['content'] = $request->input('content');

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('kb_photos', 'public');
            $validated['photo_path'] = $path;
        }

        KnowledgeBasePage::create($validated);

        return redirect()->route('knowledge-base.index')->with('success', 'Страница создана');
    }

    public function edit($id)
    {
        $page = KnowledgeBasePage::findOrFail($id);
        $categories = \App\Models\KnowledgeBaseCategory::orderBy('order')->orderBy('title')->get();
        return view('pages.knowledge_base.edit', compact('page', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $page = KnowledgeBasePage::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'order' => 'nullable|integer',
            'category_id' => 'nullable|exists:knowledge_base_categories,id',
            'photo' => 'nullable|image|max:5120',
            'remove_photo' => 'nullable|boolean',
        ]);

        $validated['content'] = $request->input('content');

        if ($request->boolean('remove_photo') && $page->photo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($page->photo_path);
            $validated['photo_path'] = null;
        } elseif ($request->hasFile('photo')) {
            if ($page->photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($page->photo_path);
            }
            $path = $request->file('photo')->store('kb_photos', 'public');
            $validated['photo_path'] = $path;
        }

        $page->update($validated);

        return redirect()->route('knowledge-base.index', ['page' => $page->id])->with('success', 'Страница обновлена');
    }

    public function destroy($id)
    {
        $page = KnowledgeBasePage::findOrFail($id);
        if ($page->photo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($page->photo_path);
        }
        $page->delete();

        return redirect()->route('knowledge-base.index')->with('success', 'Страница удалена');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $validated['order'] = $validated['order'] ?? 0;
        \App\Models\KnowledgeBaseCategory::create($validated);

        return redirect()->back()->with('success', 'Раздел создан');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = \App\Models\KnowledgeBaseCategory::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $category->update($validated);
        return redirect()->back()->with('success', 'Раздел обновлен');
    }

    public function destroyCategory($id)
    {
        $category = \App\Models\KnowledgeBaseCategory::findOrFail($id);
        $category->delete();
        return redirect()->back()->with('success', 'Раздел удален');
    }

    public function uploadImage(Request $request)
    {
        $validated = $request->validate([
            'upload' => 'required|image|max:5120',
        ]);

        if ($request->hasFile('upload')) {
            $path = $request->file('upload')->store('kb_images', 'public');
            $url = asset('storage/' . $path);

            return response()->json([
                'uploaded' => true,
                'url' => $url,
            ]);
        }

        return response()->json([
            'uploaded' => false,
            'error' => ['message' => 'Could not upload image']
        ], 400);
    }
}
