<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DraftsReports;
use App\Models\Reports;
use App\Models\Calculation;
use App\Models\Variable;

class ManagersController extends Controller
{
    public function calculation()
    {
        return view('pages.managers.calculation');
    }

    public function getVariables(Request $request)
    {
        $counteragentType = $request->query('counteragent_type');
        
        if (!in_array($counteragentType, ['inn', 'ooo'])) {
            return response()->json(['error' => 'Invalid counteragent type'], 400);
        }

        $variables = Variable::where('counteragent_type', $counteragentType)
            ->where('table_type', 'company')
            ->get()
            ->map(function ($var) {
                return [
                    'id' => $var->id,
                    'name' => $var->name,
                    'value' => $var->value,
                ];
            });

        return response()->json($variables);
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
    private function calcultating(Request $request)
    {
        $sellingType = $request->input('selling_name'); // 'ИП (ИНН)' или 'ООО (УСН)'
        $spk = $request->input('spk');

        $counteragentType = strpos($sellingType, 'ИП') !== false ? 'inn' : 'ooo';
        $variables = Variable::where('counteragent_type', $counteragentType)
            ->where('table_type', 'company')
            ->get()
            ->keyBy('name');

        $riskReserveRate = (float)($variables['RiskReserveRate']->value ?? 0.05);
        $k_log = (float)($variables['k_log']->value ?? 0.015);
        $k_fin = (float)($variables['k_fin']->value ?? 0.015);
        $k_fbr = (float)($variables['k_fbr']->value ?? 0.002);
        $k_ps_total = (float)($variables['k_ps_total']->value ?? ($k_log + $k_fin + $k_fbr));
        $k_mgr = (float)($variables['k_mgr']->value ?? 0.245);
        $rate_ndfl = (float)($variables['rate_ndfl']->value ?? 0.13);
        $rate_ausn = (float)($variables['rate_ausn']->value ?? 0.08);
        $rate_ins = (float)($variables['rate_ins']->value ?? 0.01);
        $k_bonus = (float)($variables['k_bonus']->value ?? 0.20);
        $k_spk = (float)($variables['k_spk']->value ?? 0.20);

        $sellingSum = (float)$request->selling_sum;
        $purchaseSum = (float)$request->purchase_sum;
        $quantity = (int)$request->quantity ?: 1;

        $nacenka = $sellingSum - $purchaseSum;
        $ausn = $sellingSum * $rate_ausn;
        $P1 = $nacenka - $ausn;
        
        $riskReserve = max(0, $P1 * $riskReserveRate);
        
        $premBase = max(0, $P1 - $riskReserve);

        $logisticsBonus = $premBase * $k_log;
        $finAdminBonus = $premBase * $k_fin;
        $fbrBonus = $premBase * $k_fbr;
        $premiyaTotal = $premBase * $k_ps_total;

        $managerBase = max(0, $premBase - $premiyaTotal);
        $managerSalaryBrutto = $managerBase * $k_mgr;
        $managerNdfl = $managerSalaryBrutto * $rate_ndfl;

        $socialFunds = $managerSalaryBrutto * $rate_ins;
        
        $managerPayment = $managerSalaryBrutto - $managerNdfl;       
        $totalManagerCost = $managerSalaryBrutto + $socialFunds;
        $perUnitPayment = $quantity > 0 ? $managerPayment / $quantity : 0;

        $spkPayment = 0;
        if ($spk == 'Y') {
            $spkPayment = $managerPayment * $k_spk;
            $managerPayment -= $spkPayment;
            $perUnitPayment = $quantity > 0 ? $managerPayment / $quantity : 0;
        }

        $totalTaxes = $ausn + $managerNdfl + $socialFunds;

        $companyProfit = $P1 - $riskReserve - $premiyaTotal - $totalManagerCost;

        return [
            'nacenka' => $nacenka,
            'ausn' => $ausn,
            'P1' => $P1,
            'riskReserve' => $riskReserve,
            'premBase' => $premBase,
            'logisticsBonus' => $logisticsBonus,
            'finAdminBonus' => $finAdminBonus,
            'fbrBonus' => $fbrBonus,
            'premiyaTotal' => $premiyaTotal,
            'managerBase' => $managerBase,
            'managerSalaryBrutto' => $managerSalaryBrutto,
            'managerNdfl' => $managerNdfl,
            'socialFunds' => $socialFunds,
            'totalManagerCost' => $totalManagerCost,
            'managerPayment' => $managerPayment,
            'spkPayment' => $spkPayment,
            'perUnitPayment' => $perUnitPayment,
            'totalTaxes' => $totalTaxes,
            'companyProfit' => $companyProfit,
            'spk' => $spk,
        ];
    }

    public function calculate(Request $request)
    {
        $result = $this->calcultating($request);

        return response()->json([
            'success' => true,
            'calculations' => [
                'nacenka' => round($result['nacenka'], 2),
                'ausn' => round($result['ausn'], 2),
                'P1' => round($result['P1'], 2),
                'riskReserve' => round($result['riskReserve'], 2),
                'premBase' => round($result['premBase'], 2),
                'logisticsBonus' => round($result['logisticsBonus'], 2),
                'finAdminBonus' => round($result['finAdminBonus'], 2),
                'fbrBonus' => round($result['fbrBonus'], 2),
                'premiyaTotal' => round($result['premiyaTotal'], 2),
                'managerBase' => round($result['managerBase'], 2),
                'managerSalaryBrutto' => round($result['managerSalaryBrutto'], 2),
                'managerNdfl' => round($result['managerNdfl'], 2),
                'socialFunds' => round($result['socialFunds'], 2),
                'totalManagerCost' => round($result['totalManagerCost'], 2),
                'managerPayment' => round($result['managerPayment'], 2),
                'spkPayment' => round($result['spkPayment'], 2),
                'perUnitPayment' => round($result['perUnitPayment'], 2),
                'totalTaxes' => round($result['totalTaxes'], 2),
                'companyProfit' => round($result['companyProfit'], 2),
                'spk' => $result['spk']
            ]
        ], 201);
    }

    /**
     * Summary of storeReport
     * @param Request $request
     */
    public function storeDraftsReport(Request $request)
    {
        $calculationId = $this->saveCalculation($request);

        $result = $this->calcultating($request);

        $userName = auth()->user()->name ?? 'Без имени';

        $calculation = Calculation::findOrFail($calculationId);
        $calculation->update([
            'manager_payment' => $result['managerPayment'],
            'manager_salary_brutto' => $result['managerSalaryBrutto'],
            'per_unit_payment' => $result['perUnitPayment'],
        ]);

        DraftsReports::create([
            'manager_id' => auth()->id(),
            'date' => now()->toDateString(),
            'name' => $userName,
            'amount' => $result['managerPayment'],
            'calculate_id' => $calculationId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Сохранено в отчёты',
            'calculations' => [
                'nacenka' => round($result['nacenka'], 2),
                'ausn' => round($result['ausn'], 2),
                'P1' => round($result['P1'], 2),
                'riskReserve' => round($result['riskReserve'], 2),
                'premBase' => round($result['premBase'], 2),
                'logisticsBonus' => round($result['logisticsBonus'], 2),
                'finAdminBonus' => round($result['finAdminBonus'], 2),
                'fbrBonus' => round($result['fbrBonus'], 2),
                'premiyaTotal' => round($result['premiyaTotal'], 2),
                'managerBase' => round($result['managerBase'], 2),
                'managerSalaryBrutto' => round($result['managerSalaryBrutto'], 2),
                'managerNdfl' => round($result['managerNdfl'], 2),
                'socialFunds' => round($result['socialFunds'], 2),
                'totalManagerCost' => round($result['totalManagerCost'], 2),
                'managerPayment' => round($result['managerPayment'], 2),
                'spkPayment' => round($result['spkPayment'], 2),
                'perUnitPayment' => round($result['perUnitPayment'], 2),
                'totalTaxes' => round($result['totalTaxes'], 2),
                'companyProfit' => round($result['companyProfit'], 2),
            ]
        ], 201);
    }

    /**
     * Summary of storeReport
     * @param Request $request
     */
    public function storeReport(Request $request)
    {
        $calculationId = $this->saveCalculation($request);

        $result = $this->calcultating($request);

        $userName = auth()->user()->name ?? 'Без имени';

        $calculation = Calculation::findOrFail($calculationId);
        $calculation->update([
            'manager_payment' => $result['managerPayment'],
            'manager_salary_brutto' => $result['managerSalaryBrutto'],
            'per_unit_payment' => $result['perUnitPayment'],
        ]);

        Reports::create([
            'manager_id' => auth()->id(),
            'date' => now()->toDateString(),
            'name' => $userName,
            'amount' => $result['managerPayment'],
            'calculate_id' => $calculationId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Сохранено в историю',
        ], 201);
    }

    /**
     * Summary of saveCalculation
     * @param Request $request
     * @throws \Exception
     * @return int
     */
    private function saveCalculation(Request $request): int
    {
        $validated = $request->validate([
            'buying_name' => 'nullable|string',
            'date' => 'nullable|string',
            'selling_name' => 'nullable|string',
            'spk' => 'nullable|string',
            'purchase_price' => 'nullable|numeric',
            'quantity' => 'nullable|integer',
            'purchase_sum' => 'nullable|numeric',
            'markup_percent' => 'nullable|numeric',
            'selling_price' => 'nullable|numeric',
            'selling_sum' => 'nullable|numeric',
            'prf_percent' => 'nullable|numeric',
            'deal_payment' => 'nullable|numeric',
            'per_unit_payment' => 'nullable|numeric',
        ]);

        $calculation = Calculation::create([
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        if (!$calculation) {
            throw new \Exception('Ошибка сохранения расчёта');
        }

        return $calculation->id;
    }
}
