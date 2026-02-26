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
    /** @var CalculationService */
    protected $calculationService;
    /** @var ManagerReportService */
    protected $managerReportService;
    /** @var \App\Repositories\VariableRepository */
    protected $variableRepository;
    /** @var \App\Repositories\NdsRepository */
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

    public function calculation(): \Illuminate\View\View
    {
        $history = Reports::where('manager_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        $spks = \App\Models\Spk::all();
        $suppliers = \App\Models\Supplier::all();

        return view('pages.managers.calculation', compact('history', 'spks', 'suppliers'));
    }

    public function getVariables(Request $request): \Illuminate\Http\JsonResponse
    {
        $counteragentType = $request->query('counteragent_type');

        if (!in_array($counteragentType, ['inn', 'ooo', 'fvn'])) {
            return response()->json(['error' => 'Invalid counteragent type'], 400);
        }

        /** @var string $dbCounteragentType */
        $dbCounteragentType = ($counteragentType === 'fvn') ? 'ooo' : $counteragentType;

        $variables = $this->variableRepository->getCompanyVariablesByType($dbCounteragentType)
            ->map(function ($var) {
                return [
                    'id' => $var->id,
                    'name' => $var->title,
                    'value' => $var->value,
                ];
            });

        return response()->json($variables);
    }

    public function getNds(Request $request): \Illuminate\Http\JsonResponse
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

    public function reports(): \Illuminate\View\View
    {
        $reports = $this->managerReportService->getReportsForManager(DraftsReports::class, (int)auth()->id());
        return view('pages.managers.reports', compact('reports'));
    }

    public function history(): \Illuminate\View\View
    {
        $reports = $this->managerReportService->getReportsForManager(Reports::class, (int)auth()->id());
        return view('pages.managers.history', compact('reports'));
    }

    public function calculate(Request $request): \Illuminate\Http\JsonResponse
    {
        $dto = \App\Services\Calculation\DTO\CalculationRequestDTO::fromRequest($request);
        $result = $this->calculationService->calculate($dto);

        $sellingType = (string)$request->input('selling_name');
        $counteragentType = strpos($sellingType, 'ИП (ИНН)') !== false ? 'inn' : (strpos($sellingType, 'ИП (ФВН)') !== false ? 'fvn' : 'ooo');

        $calculations = [
            'nacenka' => round((float)$result->nacenka, 0, PHP_ROUND_HALF_UP),
            'P1' => round((float)$result->P1, 0, PHP_ROUND_HALF_UP),
            'riskReserve' => round((float)$result->riskReserve, 0, PHP_ROUND_HALF_UP),
            'premBase' => round((float)$result->premBase, 0, PHP_ROUND_HALF_UP),
            'logisticsBonus' => round((float)$result->logisticsBonus, 0, PHP_ROUND_HALF_UP),
            'finAdminBonus' => round((float)$result->finAdminBonus, 0, PHP_ROUND_HALF_UP),
            'fbrBonus' => round((float)$result->fbrBonus, 0, PHP_ROUND_HALF_UP),
            'premiyaTotal' => round((float)$result->premiyaTotal, 0, PHP_ROUND_HALF_UP),
            'managerBase' => round((float)$result->managerBase, 0, PHP_ROUND_HALF_UP),
            'managerSalaryBrutto' => round((float)$result->managerSalaryBrutto, 0, PHP_ROUND_HALF_UP),
            'managerNdfl' => round((float)$result->managerNdfl, 0, PHP_ROUND_HALF_UP),
            'socialFunds' => round((float)$result->socialFunds, 0, PHP_ROUND_HALF_UP),
            'totalManagerCost' => round((float)$result->totalManagerCost, 0, PHP_ROUND_HALF_UP),
            'managerPayment' => round((float)$result->managerPayment, 0, PHP_ROUND_HALF_UP),
            'spkPayment' => round((float)$result->spkPayment, 0, PHP_ROUND_HALF_UP),
            'perUnitPayment' => round((float)$result->perUnitPayment, 0, PHP_ROUND_HALF_UP),
            'totalTaxes' => round((float)$result->totalTaxes, 0, PHP_ROUND_HALF_UP),
            'companyProfit' => round((float)$result->companyProfit, 0, PHP_ROUND_HALF_UP),
            'prfPercent' => round((float)$result->prfPercent, 2, PHP_ROUND_HALF_UP),
            'spk' => $result->spk,
            'inTheDeal' => round((float)$result->inTheDeal, 0, PHP_ROUND_HALF_UP),
            'sellingSumPerUnit' => round((float)$result->sellingSumPerUnit, 0, PHP_ROUND_HALF_UP),
            'sellingSumTotal' => round((float)$result->sellingSumTotal, 0, PHP_ROUND_HALF_UP),
        ];

        if ($counteragentType === 'inn') {
            $calculations['ausn'] = round((float)$result->ausn, 0, PHP_ROUND_HALF_UP);
        }

        if ($counteragentType === 'ooo' || $counteragentType === 'fvn') {
            $calculations['ndsOutgoing'] = round((float)$result->ndsOutgoing, 0, PHP_ROUND_HALF_UP);
            $calculations['ndsIncoming'] = round((float)$result->ndsIncoming, 0, PHP_ROUND_HALF_UP);
            $calculations['ndsPaid'] = round((float)$result->ndsPaid, 0, PHP_ROUND_HALF_UP);
            $calculations['citBase'] = round((float)$result->citBase, 0, PHP_ROUND_HALF_UP);
            $calculations['citTax'] = round((float)$result->citTax, 0, PHP_ROUND_HALF_UP);
        }

        return response()->json([
            'success' => true,
            'calculations' => $calculations
        ], 201);
    }

    public function storeDraftsReport(StoreManagerReportRequest $request): \Illuminate\Http\JsonResponse
    {
        $calculationId = (int)$request->query('calculation_id');
        $report = $this->managerReportService->saveReport($request->validated(), DraftsReports::class, (int)auth()->id(), false, $calculationId);

        return $this->formatSaveResponse($request, $report, 'Сохранено в отчёты', true);
    }

    public function storeReport(StoreManagerReportRequest $request): \Illuminate\Http\JsonResponse
    {
        $report = $this->managerReportService->saveReport($request->validated(), Reports::class, (int)auth()->id(), true);

        return $this->formatSaveResponse($request, $report, 'Сохранено в историю', false);
    }

    protected function formatSaveResponse(Request $request, \Illuminate\Database\Eloquent\Model $report, string $message, bool $includeCalculations): \Illuminate\Http\JsonResponse
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
                'nacenka' => round((float)$result->nacenka, 0, PHP_ROUND_HALF_UP),
                'P1' => round((float)$result->P1, 0, PHP_ROUND_HALF_UP),
                'riskReserve' => round((float)$result->riskReserve, 0, PHP_ROUND_HALF_UP),
                'premBase' => round((float)$result->premBase, 0, PHP_ROUND_HALF_UP),
                'logisticsBonus' => round((float)$result->logisticsBonus, 0, PHP_ROUND_HALF_UP),
                'finAdminBonus' => round((float)$result->finAdminBonus, 0, PHP_ROUND_HALF_UP),
                'fbrBonus' => round((float)$result->fbrBonus, 0, PHP_ROUND_HALF_UP),
                'premiyaTotal' => round((float)$result->premiyaTotal, 0, PHP_ROUND_HALF_UP),
                'managerBase' => round((float)$result->managerBase, 0, PHP_ROUND_HALF_UP),
                'managerSalaryBrutto' => round((float)$result->managerSalaryBrutto, 0, PHP_ROUND_HALF_UP),
                'managerNdfl' => round((float)$result->managerNdfl, 0, PHP_ROUND_HALF_UP),
                'socialFunds' => round((float)$result->socialFunds, 0, PHP_ROUND_HALF_UP),
                'totalManagerCost' => round((float)$result->totalManagerCost, 0, PHP_ROUND_HALF_UP),
                'managerPayment' => round((float)$result->managerPayment, 0, PHP_ROUND_HALF_UP),
                'spkPayment' => round((float)$result->spkPayment, 0, PHP_ROUND_HALF_UP),
                'perUnitPayment' => round((float)$result->perUnitPayment, 0, PHP_ROUND_HALF_UP),
                'totalTaxes' => round((float)$result->totalTaxes, 0, PHP_ROUND_HALF_UP),
                'companyProfit' => round((float)$result->companyProfit, 0, PHP_ROUND_HALF_UP),
                'prfPercent' => round((float)$result->prfPercent, 2, PHP_ROUND_HALF_UP),
            ];

            if ($counteragentType === 'inn') {
                $response['calculations']['ausn'] = round((float)$result->ausn, 0, PHP_ROUND_HALF_UP);
            } else {
                $response['calculations']['ndsOutgoing'] = round((float)$result->ndsOutgoing, 0, PHP_ROUND_HALF_UP);
                $response['calculations']['ndsIncoming'] = round((float)$result->ndsIncoming, 0, PHP_ROUND_HALF_UP);
                $response['calculations']['ndsPaid'] = round((float)$result->ndsPaid, 0, PHP_ROUND_HALF_UP);
                $response['calculations']['citBase'] = round((float)$result->citBase, 0, PHP_ROUND_HALF_UP);
                $response['calculations']['citTax'] = round((float)$result->citTax, 0, PHP_ROUND_HALF_UP);
            }
        }

        return response()->json($response, 201);
    }

    public function getReport(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $type = (string)$request->query('type', 'history');
        $modelClass = ($type === 'draft') ? DraftsReports::class : Reports::class;

        $data = $this->managerReportService->getReportWithCalculation($modelClass, $id, (int)auth()->id());

        if (empty($data)) {
            abort(403);
        }

        return response()->json(array_merge(['success' => true], $data));
    }
}
