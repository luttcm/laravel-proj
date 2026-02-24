<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSoldFromCompanyRequest;
use App\Http\Requests\UpdateSoldFromCompanyRequest;
use App\Repositories\SoldFromCompanyRepository;
use Illuminate\Http\Request;

class SoldFromCompanyController extends Controller
{
    protected $companyRepository;

    public function __construct(SoldFromCompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function index()
    {
        $companies = $this->companyRepository->getAllPaginated(10);
        return view("pages.sold_from_companies.index", compact("companies"));
    }

    public function add()
    {
        return view("pages.sold_from_companies.add");
    }

    public function store(StoreSoldFromCompanyRequest $request)
    {
        $this->companyRepository->create($request->validated());

        return redirect()->route('sold-from-companies.index')
            ->with('success', "Компания \"{$request->validated()['name']}\" добавлена!");
    }

    public function edit($id)
    {
        $company = $this->companyRepository->findById($id);
        return view('pages.sold_from_companies.edit', compact('company'));
    }

    public function update(UpdateSoldFromCompanyRequest $request, $id)
    {
        $this->companyRepository->update($id, $request->validated());

        return redirect()->route('sold-from-companies.index')
            ->with('success', 'Компания обновлена');
    }

    public function delete($id)
    {
        $this->companyRepository->delete($id);
        return redirect()->route('sold-from-companies.index')
            ->with('success', 'Компания удалена');
    }
}
