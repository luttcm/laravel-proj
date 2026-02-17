<?php

namespace App\Http\Controllers;

use App\Models\SoldFromCompany;
use Illuminate\Http\Request;

class SoldFromCompanyController extends Controller
{
    public function index()
    {
        $companies = SoldFromCompany::paginate(10);
        return view("pages.sold_from_companies.index", compact("companies"));
    }

    public function add()
    {
        return view("pages.sold_from_companies.add");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sold_from_companies,name',
        ]);

        SoldFromCompany::create($validated);

        return redirect()->route('sold-from-companies.index')
            ->with('success', "Компания \"{$validated['name']}\" добавлена!");
    }

    public function edit($id)
    {
        $company = SoldFromCompany::findOrFail($id);
        return view('pages.sold_from_companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $company = SoldFromCompany::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sold_from_companies,name,' . $id,
        ]);

        $company->update($validated);

        return redirect()->route('sold-from-companies.index')
            ->with('success', 'Компания обновлена');
    }

    public function delete($id)
    {
        $company = SoldFromCompany::findOrFail($id);
        $company->delete();
        return redirect()->route('sold-from-companies.index')
            ->with('success', 'Компания удалена');
    }
}
