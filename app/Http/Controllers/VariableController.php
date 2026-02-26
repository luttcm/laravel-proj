<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVariableRequest;
use App\Http\Requests\UpdateVariableRequest;
use App\Repositories\VariableRepository;
use App\Services\VariableService;
use Illuminate\Http\Request;
use Exception;

class VariableController extends Controller
{
    /** @var VariableRepository */
    protected $variableRepository;
    /** @var VariableService */
    protected $variableService;

    public function __construct(VariableRepository $variableRepository, VariableService $variableService)
    {
        $this->variableRepository = $variableRepository;
        $this->variableService = $variableService;
    }

    public function index(): \Illuminate\View\View
    {
        $companyVariables = $this->variableRepository->getAllCompanyPaginated(10);
        $fncVariables = $this->variableRepository->getAllFncPaginated(10);
        return view("pages.variables.index", compact("companyVariables", "fncVariables"));
    }

    public function add(): \Illuminate\View\View
    {
        $types = ["float", "integer"];
        return view("pages.variables.add", compact("types"));
    }

    public function store(StoreVariableRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->variableService->createVariable($request->validated());
            return redirect()->route('variables.index')->with('success', "Переменная создана!");
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(int $id): \Illuminate\View\View
    {
        $variable = $this->variableRepository->findById($id);
        $types = ["float", "integer"];
        return view('pages.variables.edit', compact('variable', 'types'));
    }

    public function update(UpdateVariableRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->variableService->updateVariable($id, $request->validated());
            return redirect()->route('variables.index')->with('success', 'Переменная обновлена');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function delete(int $id): \Illuminate\Http\RedirectResponse
    {
        $this->variableService->deleteVariable($id);
        return redirect()->route('variables.index')->with('success', 'Переменная удалена');
    }
}
