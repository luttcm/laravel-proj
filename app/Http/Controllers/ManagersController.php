<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManagerReportRequest;
use App\Services\Calculation\CalculationService;
use App\Services\ManagerReportService;
use App\Models\DraftsReports;
use App\Models\Reports;
use App\Models\Variable;
use Illuminate\Http\Request;

class ManagersController extends Controller
{
    protected $calculationService;
    protected $managerReportService;
    protected $variableRepository;
    protected $ndsRepository;

    public function __construct(
        CalculationService $calculationService,
        ManagerReportService $managerReportService,
        \App\Repositories\VariableRepository $variableRepository,
        \App\Repositories\NdsRepository $ndsRepository
    ) {
        $this->calculationService = $calculationService;
        $this->managerReportService = $managerReportService;
        $this->variableRepository = $variableRepository;
        $this->ndsRepository = $ndsRepository;
    }

    public function calculation()
    {
        $history = Reports::where('manager_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        $spks = \App\Models\Spk::all();
        $suppliers = \App\Models\Supplier::all();

        return view('pages.managers.calculation', compact('history', 'spks', 'suppliers'));
    }

    public function getVariables(Request $request)
    {
        $counteragentType = $request->query('counteragent_type');

        if (!in_array($counteragentType, ['inn', 'ooo', 'fvn'])) {
            return response()->json(['error' => 'Invalid counteragent type'], 400);
        }

        $dbCounteragentType = ($counteragentType === 'fvn') ? 'ooo' : $counteragentType;

        $variables = $this->variableRepository->getCompanyVariablesByType($dbCounteragentType)
            ->map(function ($var) {
                return [
                    'id' => $var->id,
                    'name' => $var->report_title,
                    'value' => $var->value,
                ];
            });

        return response()->json($variables);
    }

    public function getNds(Request $request)
    {
        $ndsList = $this->ndsRepository->getAll()->map(function ($nds) {
            return [
                'id' => $nds->id,
                'title' => $nds->title,
                'code_name' => $nds->code_name,
                'percent' => $nds->percent,
            ];
        });

        return response()->json($ndsList);
    }

    public function reports()
    {
        $reports = $this->managerReportService->getReportsForManager(DraftsReports::class, auth()->id());
        return view('pages.managers.reports', compact('reports'));
    }

    public function history()
    {
        $reports = $this->managerReportService->getReportsForManager(Reports::class, auth()->id());
        return view('pages.managers.history', compact('reports'));
    }

    public function calculate(Request $request)
    {
        $dto = \App\Services\Calculation\DTO\CalculationRequestDTO::fromRequest($request);
        $result = $this->calculationService->calculate($dto);

        $sellingType = $request->input('selling_name');
        $counteragentType = strpos($sellingType, 'ИП (ИНН)') !== false ? 'inn' : (strpos($sellingType, 'ИП (ФВН)') !== false ? 'fvn' : 'ooo');

        $calculations = [
            'nacenka' => round($result->nacenka, 0, PHP_ROUND_HALF_UP),
            'P1' => round($result->P1, 0, PHP_ROUND_HALF_UP),
            'riskReserve' => round($result->riskReserve, 0, PHP_ROUND_HALF_UP),
            'premBase' => round($result->premBase, 0, PHP_ROUND_HALF_UP),
            'logisticsBonus' => round($result->logisticsBonus, 0, PHP_ROUND_HALF_UP),
            'finAdminBonus' => round($result->finAdminBonus, 0, PHP_ROUND_HALF_UP),
            'fbrBonus' => round($result->fbrBonus, 0, PHP_ROUND_HALF_UP),
            'premiyaTotal' => round($result->premiyaTotal, 0, PHP_ROUND_HALF_UP),
            'managerBase' => round($result->managerBase, 0, PHP_ROUND_HALF_UP),
            'managerSalaryBrutto' => round($result->managerSalaryBrutto, 0, PHP_ROUND_HALF_UP),
            'managerNdfl' => round($result->managerNdfl, 0, PHP_ROUND_HALF_UP),
            'socialFunds' => round($result->socialFunds, 0, PHP_ROUND_HALF_UP),
            'totalManagerCost' => round($result->totalManagerCost, 0, PHP_ROUND_HALF_UP),
            'managerPayment' => round($result->managerPayment, 0, PHP_ROUND_HALF_UP),
            'spkPayment' => round($result->spkPayment, 0, PHP_ROUND_HALF_UP),
            'perUnitPayment' => round($result->perUnitPayment, 0, PHP_ROUND_HALF_UP),
            'totalTaxes' => round($result->totalTaxes, 0, PHP_ROUND_HALF_UP),
            'companyProfit' => round($result->companyProfit, 0, PHP_ROUND_HALF_UP),
            'prfPercent' => round($result->prfPercent, 2, PHP_ROUND_HALF_UP),
            'spk' => $result->spk,
            'inTheDeal' => round($result->inTheDeal, 0, PHP_ROUND_HALF_UP),
            'sellingSumPerUnit' => round($result->sellingSumPerUnit, 0, PHP_ROUND_HALF_UP),
            'sellingSumTotal' => round($result->sellingSumTotal, 0, PHP_ROUND_HALF_UP),
        ];

        if ($counteragentType === 'inn') {
            $calculations['ausn'] = round($result->ausn, 0, PHP_ROUND_HALF_UP);
        }

        if ($counteragentType === 'ooo' || $counteragentType === 'fvn') {
            $calculations['ndsOutgoing'] = round($result->ndsOutgoing, 0, PHP_ROUND_HALF_UP);
            $calculations['ndsIncoming'] = round($result->ndsIncoming, 0, PHP_ROUND_HALF_UP);
            $calculations['ndsPaid'] = round($result->ndsPaid, 0, PHP_ROUND_HALF_UP);
            $calculations['citBase'] = round($result->citBase, 0, PHP_ROUND_HALF_UP);
            $calculations['citTax'] = round($result->citTax, 0, PHP_ROUND_HALF_UP);
        }

        return response()->json([
            'success' => true,
            'calculations' => $calculations
        ], 201);
    }

    public function storeDraftsReport(StoreManagerReportRequest $request)
    {
        $calculationId = $request->query('calculation_id');
        $report = $this->managerReportService->saveReport($request->validated(), DraftsReports::class, auth()->id(), false, $calculationId);

        return $this->formatSaveResponse($request, $report, 'Сохранено в отчёты', true);
    }

    public function storeReport(StoreManagerReportRequest $request)
    {
        $report = $this->managerReportService->saveReport($request->validated(), Reports::class, auth()->id(), true);

        return $this->formatSaveResponse($request, $report, 'Сохранено в историю', false);
    }

    protected function formatSaveResponse(Request $request, $report, string $message, bool $includeCalculations)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($includeCalculations) {
            $dto = \App\Services\Calculation\DTO\CalculationRequestDTO::fromRequest($request);
            $result = $this->calculationService->calculate($dto);
            
            $sellingType = $request->input('selling_name');
            $counteragentType = strpos($sellingType, 'ИП (ИНН)') !== false ? 'inn' : (strpos($sellingType, 'ИП (ФВН)') !== false ? 'fvn' : 'ooo');

            $response['calculations'] = [
                'nacenka' => round($result->nacenka, 0, PHP_ROUND_HALF_UP),
                'P1' => round($result->P1, 0, PHP_ROUND_HALF_UP),
                'riskReserve' => round($result->riskReserve, 0, PHP_ROUND_HALF_UP),
                'premBase' => round($result->premBase, 0, PHP_ROUND_HALF_UP),
                'logisticsBonus' => round($result->logisticsBonus, 0, PHP_ROUND_HALF_UP),
                'finAdminBonus' => round($result->finAdminBonus, 0, PHP_ROUND_HALF_UP),
                'fbrBonus' => round($result->fbrBonus, 0, PHP_ROUND_HALF_UP),
                'premiyaTotal' => round($result->premiyaTotal, 0, PHP_ROUND_HALF_UP),
                'managerBase' => round($result->managerBase, 0, PHP_ROUND_HALF_UP),
                'managerSalaryBrutto' => round($result->managerSalaryBrutto, 0, PHP_ROUND_HALF_UP),
                'managerNdfl' => round($result->managerNdfl, 0, PHP_ROUND_HALF_UP),
                'socialFunds' => round($result->socialFunds, 0, PHP_ROUND_HALF_UP),
                'totalManagerCost' => round($result->totalManagerCost, 0, PHP_ROUND_HALF_UP),
                'managerPayment' => round($result->managerPayment, 0, PHP_ROUND_HALF_UP),
                'spkPayment' => round($result->spkPayment, 0, PHP_ROUND_HALF_UP),
                'perUnitPayment' => round($result->perUnitPayment, 0, PHP_ROUND_HALF_UP),
                'totalTaxes' => round($result->totalTaxes, 0, PHP_ROUND_HALF_UP),
                'companyProfit' => round($result->companyProfit, 0, PHP_ROUND_HALF_UP),
                'prfPercent' => round($result->prfPercent, 2, PHP_ROUND_HALF_UP),
            ];

            if ($counteragentType === 'inn') {
                $response['calculations']['ausn'] = round($result->ausn, 0, PHP_ROUND_HALF_UP);
            } else {
                $response['calculations']['ndsOutgoing'] = round($result->ndsOutgoing, 0, PHP_ROUND_HALF_UP);
                $response['calculations']['ndsIncoming'] = round($result->ndsIncoming, 0, PHP_ROUND_HALF_UP);
                $response['calculations']['ndsPaid'] = round($result->ndsPaid, 0, PHP_ROUND_HALF_UP);
                $response['calculations']['citBase'] = round($result->citBase, 0, PHP_ROUND_HALF_UP);
                $response['calculations']['citTax'] = round($result->citTax, 0, PHP_ROUND_HALF_UP);
            }
        }

        return response()->json($response, 201);
    }

    public function getReport(Request $request, $id)
    {
        $type = $request->query('type', 'history');
        $modelClass = ($type === 'draft') ? DraftsReports::class : Reports::class;

        $data = $this->managerReportService->getReportWithCalculation($modelClass, $id, auth()->id());

        if (empty($data)) {
            abort(403);
        }

        return response()->json(array_merge(['success' => true], $data));
    }
}
