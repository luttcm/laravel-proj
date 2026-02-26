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
    /** @var FinReportService */
    protected $finReportService;

    public function __construct(
        \App\Services\Calculation\CalculationService $calculationService,
        FinReportService $finReportService,
        ManagerReportService $managerReportService,
        \App\Repositories\VariableRepository $variableRepository,
        \App\Repositories\NdsRepository $ndsRepository
    ) {
        parent::__construct($calculationService, $managerReportService, $variableRepository, $ndsRepository);
        $this->finReportService = $finReportService;
    }

    public function calculation(): \Illuminate\View\View
    {
        $spks = \App\Models\Spk::all();
        $suppliers = \App\Models\Supplier::all();
        return view('pages.findirector.calculation', compact('spks', 'suppliers'));
    }

    public function reports(): \Illuminate\View\View
    {
        $reports = $this->managerReportService->getReportsForManager(DraftsReports::class, (int)auth()->id());
        return view('pages.findirector.reports', compact('reports'));
    }

    public function history(): \Illuminate\View\View
    {
        $reports = $this->managerReportService->getReportsForManager(Reports::class, (int)auth()->id());
        return view('pages.findirector.history', compact('reports'));
    }

    /**
     * @OA\Get(
     *     path="/findirector/fin-reports",
     *     summary="Список финансовых отчетов",
     *     tags={"FinDirector"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Список отчетов")
     * )
     */
    public function finReportsIndex(): \Illuminate\View\View
    {
        $reports = $this->finReportService->getPaginatedForUser((int)auth()->id());
        return view('pages.findirector.fin_reports.index', compact('reports'));
    }

    public function finReportsAdd(): \Illuminate\View\View
    {
        $spks = \App\Models\Spk::all();
        $suppliers = \App\Models\Supplier::all();
        $nds = \App\Models\Nds::all();
        $sellingCompanies = \App\Models\SoldFromCompany::all();
        $companyVariables = \App\Models\Variable::where('table_type', 'company')->get();
        return view('pages.findirector.fin_reports.add', compact('spks', 'suppliers', 'nds', 'sellingCompanies', 'companyVariables'));
    }

    /**
     * @OA\Post(
     *     path="/findirector/fin-reports",
     *     summary="Создать финансовый отчет",
     *     tags={"FinDirector"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"report_title", "deal_payment"},
     *                 @OA\Property(property="report_title", type="string"),
     *                 @OA\Property(property="deal_payment", type="number", format="float")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=302, description="Перенаправление")
     * )
     */
    public function finReportsStore(StoreFinReportRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->finReportService->createReport($request->validated(), (int)auth()->id());

        return redirect()->route('findirector.fin-reports.index')
            ->with('success', 'Отчет успешно создан');
    }

    public function finReportsEdit(int $id): \Illuminate\View\View
    {
        $report = $this->finReportService->findForUser($id, (int)auth()->id());

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

    /**
     * @OA\Put(
     *     path="/findirector/fin-reports/{id}",
     *     summary="Обновить финансовый отчет",
     *     tags={"FinDirector"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"report_title", "deal_payment"},
     *                 @OA\Property(property="report_title", type="string"),
     *                 @OA\Property(property="deal_payment", type="number", format="float")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=302, description="Перенаправление"),
     *     @OA\Response(response=403, description="Доступ запрещен"),
     *     @OA\Response(response=404, description="Отчет не найден")
     * )
     */
    public function finReportsUpdate(UpdateFinReportRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $result = $this->finReportService->updateReport($id, $request->validated(), (int)auth()->id());

        if (!$result) {
            abort(403);
        }

        return redirect()->route('findirector.fin-reports.index')
            ->with('success', 'Отчет успешно обновлен');
    }

    /**
     * @OA\Post(
     *     path="/findirector/fin-reports/{id}/delete",
     *     summary="Удалить финансовый отчет",
     *     tags={"FinDirector"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=302, description="Перенаправление"),
     *     @OA\Response(response=403, description="Доступ запрещен")
     * )
     */
    public function finReportsDelete(int $id): \Illuminate\Http\RedirectResponse
    {
        $result = $this->finReportService->deleteReport($id, (int)auth()->id());

        if (!$result) {
            abort(403);
        }

        return redirect()->route('findirector.fin-reports.index')
            ->with('success', 'Отчет удален');
    }
}
