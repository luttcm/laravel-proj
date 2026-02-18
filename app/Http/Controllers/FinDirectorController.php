<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFinReportRequest;
use App\Http\Requests\UpdateFinReportRequest;
use App\Services\FinReportService;
use App\Services\ManagerReportService;
use App\Models\DraftsReports;
use App\Models\Reports;
use Illuminate\Http\Request;

class FinDirectorController extends ManagersController
{
    protected $finReportService;

    public function __construct(
        \App\Services\Calculation\CalculationService $calculationService,
        FinReportService $finReportService,
        ManagerReportService $managerReportService
    ) {
        parent::__construct($calculationService, $managerReportService);
        $this->finReportService = $finReportService;
    }

    public function calculation()
    {
        $spks = \App\Models\Spk::all();
        $suppliers = \App\Models\Supplier::all();
        return view('pages.findirector.calculation', compact('spks', 'suppliers'));
    }

    public function reports()
    {
        $reports = $this->managerReportService->getReportsForManager(DraftsReports::class, auth()->id());
        return view('pages.findirector.reports', compact('reports'));
    }

    public function history()
    {
        $reports = $this->managerReportService->getReportsForManager(Reports::class, auth()->id());
        return view('pages.findirector.history', compact('reports'));
    }

    public function finReportsIndex()
    {
        $reports = $this->finReportService->getPaginatedForUser(auth()->id());
        return view('pages.findirector.fin_reports.index', compact('reports'));
    }

    public function finReportsAdd()
    {
        $spks = \App\Models\Spk::all();
        $suppliers = \App\Models\Supplier::all();
        $nds = \App\Models\Nds::all();
        $sellingCompanies = \App\Models\SoldFromCompany::all();
        $companyVariables = \App\Models\Variable::where('table_type', 'company')->get();
        return view('pages.findirector.fin_reports.add', compact('spks', 'suppliers', 'nds', 'sellingCompanies', 'companyVariables'));
    }

    public function finReportsStore(StoreFinReportRequest $request)
    {
        $this->finReportService->createReport($request->validated(), auth()->id());

        return redirect()->route('findirector.fin-reports.index')
            ->with('success', 'Отчет успешно создан');
    }

    public function finReportsEdit($id)
    {
        $report = $this->finReportService->findForUser($id, auth()->id());

        if (!$report) {
            abort(403);
        }

        $spks = \App\Models\Spk::all();
        $suppliers = \App\Models\Supplier::all();
        $nds = \App\Models\Nds::all();
        $sellingCompanies = \App\Models\SoldFromCompany::all();
        $companyVariables = \App\Models\Variable::where('table_type', 'company')->get();
        return view('pages.findirector.fin_reports.edit', compact('report', 'spks', 'suppliers', 'nds', 'sellingCompanies', 'companyVariables'));
    }

    public function finReportsUpdate(UpdateFinReportRequest $request, $id)
    {
        $result = $this->finReportService->updateReport($id, $request->validated(), auth()->id());

        if (!$result) {
            abort(403);
        }

        return redirect()->route('findirector.fin-reports.index')
            ->with('success', 'Отчет успешно обновлен');
    }

    public function finReportsDelete($id)
    {
        $result = $this->finReportService->deleteReport($id, auth()->id());

        if (!$result) {
            abort(403);
        }

        return redirect()->route('findirector.fin-reports.index')
            ->with('success', 'Отчет удален');
    }
}
