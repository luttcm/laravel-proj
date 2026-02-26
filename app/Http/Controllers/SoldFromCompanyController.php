<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSoldFromCompanyRequest;
use App\Http\Requests\UpdateSoldFromCompanyRequest;
use App\Repositories\SoldFromCompanyRepository;
use Illuminate\Http\Request;

class SoldFromCompanyController extends Controller
{
    /** @var SoldFromCompanyRepository */
    protected $companyRepository;

    public function __construct(SoldFromCompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function index(): \Illuminate\View\View
    {
        $companies = $this->companyRepository->getAllPaginated(10);
        return view("pages.sold_from_companies.index", compact("companies"));
    }

    public function add(): \Illuminate\View\View
    {
        return view("pages.sold_from_companies.add");
    }

    public function store(StoreSoldFromCompanyRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->companyRepository->create($request->validated());

        return redirect()->route('sold-from-companies.index')
            ->with('success', "Компания \"{$request->validated()['name']}\" добавлена!");
    }

    public function edit(int $id): \Illuminate\View\View
    {
        $company = $this->companyRepository->findById($id);
        return view('pages.sold_from_companies.edit', compact('company'));
    }

    public function update(UpdateSoldFromCompanyRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $this->companyRepository->update($id, $request->validated());

        return redirect()->route('sold-from-companies.index')
            ->with('success', 'Компания обновлена');
    }

    public function delete(int $id): \Illuminate\Http\RedirectResponse
    {
        $this->companyRepository->delete($id);
        return redirect()->route('sold-from-companies.index')
            ->with('success', 'Компания удалена');
    }
}
