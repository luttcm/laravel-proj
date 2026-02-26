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
        $history = $this->managerReportService->getLatestReports((int)auth()->id());
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

        return response()->json([
            'success' => true,
            'calculations' => $result->formatForResponse()
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
            $response['calculations'] = $result->formatForResponse();
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
