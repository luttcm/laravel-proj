<?php

namespace App\Http\Controllers;

use App\Models\Nds;
use Illuminate\Http\Request;

class NdsController extends Controller
{
    public function index()
    {
        $nds = Nds::paginate(10);
        return view("pages.nds.index", compact("nds"));
    }

     public function add()
    {
        return view("pages.nds.add");
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'code_name' => 'required|string|max:50',
            'title' => 'required|string|max:50',
            'percent' => 'required|string|min:1',
        ]);

        $value = trim($validated['percent']);

        if (strpos((string)$value, ',') == true) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Используйте точку в качестве разделителя десятичных чисел');
        }
        if (!is_numeric($value)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Дробное число: введите число без букв');
        }
        $value = (float)$value;
        if (strpos((string)$value, '.') === false) {
            $value = $value . '.0';
        }

        Nds::create([
            'code_name' => $validated['code_name'],
            'title' => $validated['title'],
            'percent' => (string)$value,
        ]);

        return redirect()->route('nds.index')
            ->with('success', "НДС создан!");
    }

    public function edit($id)
    {
        $nds = Nds::findOrFail($id);
        return view('pages.nds.edit', compact('nds'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'code_name' => 'required|string|max:50',
            'title' => 'required|string|max:50',
            'percent' => 'required|string|min:1',
        ]);

        $value = trim($validated['percent']);

        if (strpos((string)$value, ',') == true) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Используйте точку в качестве разделителя десятичных чисел');
        }
        if (!is_numeric($value)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Дробное число: введите число без букв');
        }
        $value = (float)$value;
        if (strpos((string)$value, '.') === false) {
            $value = $value . '.0';
        }

        $nds = Nds::findOrFail($id);
        $nds->code_name = $validated['code_name'];
        $nds->title = $validated['title'];
        $nds->percent = (string)$value;
        $nds->save();

        return redirect()->route('nds.index')->with('success', 'НДС обновлен');
    }

    public function delete($id)
    {
        $nds = Nds::findOrFail($id);
        $nds->delete();
        return redirect()->route('nds.index')->with('success', 'НДС удален');
    }
}
