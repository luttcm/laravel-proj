<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use Illuminate\Http\Request;

class SpkController extends Controller
{
    public function index()
    {
        $spks = Spk::paginate(10);
        return view("pages.spk.index", compact("spks"));
    }

    public function add()
    {
        return view("pages.spk.add");
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'fio' => 'required|string|max:255',
            'coefficient' => 'required|numeric',
        ]);

        Spk::create($validated);

        return redirect()->route('spk.index')
            ->with('success', "СПК создан!");
    }

    public function edit($id)
    {
        $spk = Spk::findOrFail($id);
        return view('pages.spk.edit', compact('spk'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'fio' => 'required|string|max:255',
            'coefficient' => 'required|numeric',
        ]);

        $spk = Spk::findOrFail($id);
        $spk->update($validated);

        return redirect()->route('spk.index')->with('success', 'СПК обновлен');
    }

    public function delete($id)
    {
        $spk = Spk::findOrFail($id);
        $spk->delete();
        return redirect()->route('spk.index')->with('success', 'СПК удален');
    }
}
