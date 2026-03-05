<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KnowledgeBasePage;
use Illuminate\Support\Facades\Auth;

class KnowledgeBaseController extends Controller
{
    public function index(Request $request)
    {
        $pages = KnowledgeBasePage::orderBy('order')->orderBy('title')->get();
        $selectedPage = null;

        if ($request->has('page')) {
            $selectedPage = KnowledgeBasePage::find($request->page);
        } elseif ($pages->count() > 0) {
            $selectedPage = $pages->first();
        }

        return view('pages.knowledge_base.index', compact('pages', 'selectedPage'));
    }

    public function create()
    {
        return view('pages.knowledge_base.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'order' => 'nullable|integer',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['order'] = $validated['order'] ?? 0;

        KnowledgeBasePage::create($validated);

        return redirect()->route('knowledge-base.index')->with('success', 'Страница создана');
    }

    public function edit($id)
    {
        $page = KnowledgeBasePage::findOrFail($id);
        return view('pages.knowledge_base.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $page = KnowledgeBasePage::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'order' => 'nullable|integer',
        ]);

        $page->update($validated);

        return redirect()->route('knowledge-base.index', ['page' => $page->id])->with('success', 'Страница обновлена');
    }

    public function destroy($id)
    {
        $page = KnowledgeBasePage::findOrFail($id);
        $page->delete();

        return redirect()->route('knowledge-base.index')->with('success', 'Страница удалена');
    }
}
