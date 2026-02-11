<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::paginate(10);
        return view("pages.supplier.index", compact("suppliers"));
    }

    public function add()
    {
        return view("pages.supplier.add");
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'vat' => 'required|numeric',
        ]);

        Supplier::create($validated);

        return redirect()->route('supplier.index')
            ->with('success', "Поставщик создан!");
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('pages.supplier.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'vat' => 'required|numeric',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update($validated);

        return redirect()->route('supplier.index')->with('success', 'Поставщик обновлен');
    }

    public function delete($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return redirect()->route('supplier.index')->with('success', 'Поставщик удален');
    }
}
