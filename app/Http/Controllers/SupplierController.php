<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Repositories\SupplierRepository;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /** @var SupplierRepository */
    protected $supplierRepository;

    public function __construct(SupplierRepository $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    public function index(): \Illuminate\View\View
    {
        $suppliers = $this->supplierRepository->getAllPaginated(10);
        return view("pages.supplier.index", compact("suppliers"));
    }

    public function add(): \Illuminate\View\View
    {
        return view("pages.supplier.add");
    }

    public function store(StoreSupplierRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->supplierRepository->create($request->validated());

        return redirect()->route('supplier.index')
            ->with('success', "Поставщик создан!");
    }

    public function edit(int $id): \Illuminate\View\View
    {
        $supplier = $this->supplierRepository->findById($id);
        return view('pages.supplier.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $this->supplierRepository->update($id, $request->validated());

        return redirect()->route('supplier.index')->with('success', 'Поставщик обновлен');
    }

    public function delete(int $id): \Illuminate\Http\RedirectResponse
    {
        $this->supplierRepository->delete($id);
        return redirect()->route('supplier.index')->with('success', 'Поставщик удален');
    }
}
