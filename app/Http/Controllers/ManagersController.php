<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DraftsReports;
use App\Models\Reports;
use App\Models\Calculation;
use App\Models\Variable;

class ManagersController extends Controller
{
    protected $calculationService;

    public function __construct(\App\Services\Calculation\CalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }

    public function calculation()
    {
        $history = Reports::where('manager_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        return view('pages.managers.calculation', compact('history'));
    }

    public function getVariables(Request $request)
    {
        $counteragentType = $request->query('counteragent_type');
        
        if (!in_array($counteragentType, ['inn', 'ooo', 'fvn'])) {
            return response()->json(['error' => 'Invalid counteragent type'], 400);
        }

        $dbCounteragentType = ($counteragentType === 'fvn') ? 'ooo' : $counteragentType;

        $variables = Variable::where('counteragent_type', $dbCounteragentType)
            ->where('table_type', 'company')
            ->get()
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
        $ndsList = \App\Models\Nds::all()->map(function ($nds) {
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
        $reports = DraftsReports::all()
        ->where('manager_id', auth()->id())
        ->sortByDesc('created_at');

        return view('pages.managers.reports', compact('reports'));
    }

    public function history()
    {
        $reports = Reports::all()
        ->where('manager_id', auth()->id())
        ->sortByDesc('created_at');

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
    public function storeDraftsReport(Request $request)
    {
        $calculationId = $request->query('calculation_id');
        return $this->saveReport($request, DraftsReports::class, 'Сохранено в отчёты', true, true, $calculationId);
    }

    public function storeReport(Request $request)
    {
        return $this->saveReport($request, Reports::class, 'Сохранено в историю', false, true);
    }

    private function saveReport(Request $request, string $reportModel, string $message, bool $includeCalculations = false, $isHistory = false, $existingCalculationId = null)
    {
        if ($existingCalculationId || ($request->has('calculation_id') && !empty($request->input('calculation_id')))) {
            $calculationId = $existingCalculationId ?? $request->input('calculation_id');
            $calculation = Calculation::findOrFail($calculationId);
            $calculation->update($request->all());
        } else {
            $calculationId = $this->saveCalculation($request, $isHistory);
        }

        $reportId = null;
        if ($request->has('report_id') && !empty($request->input('report_id'))) {
            $reportId = $reportModel::findOrFail($request->input('report_id'));
        }
        
        $dto = \App\Services\Calculation\DTO\CalculationRequestDTO::fromRequest($request);
        $result = $this->calculationService->calculate($dto);
        $userName = auth()->user()->name ?? 'Без имени';

        $calculation = Calculation::findOrFail($calculationId);
        $calculation->update([
            'nds_id' => $request->input('nds_id'),
            'manager_payment' => $result->managerPayment,
            'manager_salary_brutto' => $result->managerSalaryBrutto,
            'per_unit_payment' => $result->perUnitPayment,
            'in_the_deal' => $result->inTheDeal,
        ]);

        $reportData = [
            'manager_id' => auth()->id(),
            'date' => now()->toDateString(),
            'name' => $userName,
            'report_title' => $request->input('report_name') ?: 'Отчет ' . now()->format('d.m.Y H:i'),
            'amount' => $result->managerPayment,
            'calculate_id' => $calculationId,
        ];

        if ($reportId) {
            $reportId->update([
                'date' => $reportData['date'],
                'report_title' => $reportData['report_title'],
                'amount' => $reportData['amount'],
            ]);
        } else {
            $reportModel::create($reportData);
        }

        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($includeCalculations) {
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

    /**
     * Summary of saveCalculation
     * @param Request $request
     * @throws \Exception
     * @return int
     */
    private function saveCalculation(Request $request, bool $isHistory = false): int
    {
        if ($isHistory) {
            $validated = $request->validate([
                'report_name' => 'nullable|string',
                'date' => 'nullable|string',
                'selling_name' => 'nullable|string',
                'spk' => 'nullable|string',
            ]);
        } else {
            $validated = $request->validate([
                'report_name' => 'required|string',
                'buying_name' => 'required|string',
                'date' => 'required|string',
                'selling_name' => 'required|string',
                'spk' => 'required|string',
                'purchase_price' => 'required|numeric',
                'quantity' => 'required|integer',
                'purchase_sum' => 'required|numeric',
                'markup_percent' => 'required|numeric',
                'selling_price' => 'required|numeric',
                'selling_sum' => 'required|numeric',
                'prf_percent' => 'required|numeric',
                'deal_payment' => 'required|numeric',
                'per_unit_payment' => 'required|numeric',
                'in_the_hand' => 'required|numeric',
            ]);
        }
        
        $data = $request->all();

        $numericFields = [
            'purchase_price', 'purchase_sum', 'markup_percent', 'selling_price', 
            'selling_sum', 'prf_percent', 'deal_payment', 'per_unit_payment', 
            'in_the_hand', 'manager_payment', 'manager_salary_brutto', 'in_the_deal'
        ];

        foreach ($numericFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = round((float)$data[$field], 2);
            }
        }

        $calculation = Calculation::create([
            'user_id' => auth()->id(),
            ...$data,
        ]);

        if (!$calculation) {
            throw new \Exception('Ошибка сохранения расчёта');
        }

        return $calculation->id;
    }

    /**
     * Получить отчет с данными расчета для загрузки в форму
     */
    public function getReport($id)
    {
        $report = Reports::findOrFail($id);

        if ($report->manager_id !== auth()->id()) {
            abort(403);
        }

        $calculation = Calculation::findOrFail($report->calculate_id);

        return response()->json([
            'success' => true,
            'report' => $report,
            'calculation' => $calculation,
        ]);
    }
}

