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
        $sellingType = $request->input('selling_name');
        $spk = $request->input('spk');
        $inTheHand = $request->input('in_the_hand');

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
        
        if ($counteragentType === 'inn') {
            $k_mgr = (float)($variables['k_mgr']->value ?? 0.245);
            $rate_ins = (float)($variables['rate_ins']->value ?? 0.01);
        } else {
            $k_mgr = (float)($variables['k_mgr']->value ?? 0.20);
            $rate_ins = (float)($variables['rate_ins']->value ?? 0.30);
        }
        
        $rate_ndfl = (float)($variables['rate_ndfl']->value ?? 0.13);
        $k_bonus = (float)($variables['k_bonus']->value ?? 0.20);
        $k_spk = (float)($variables['k_spk']->value ?? 0.20);

        $sellingSum = (float)$request->selling_sum;
        $purchaseSum = (float)$request->purchase_sum;
        $quantity = (int)$request->quantity ?: 1;

        if ($counteragentType === 'inn') {
            return $this->calculateInn($sellingSum, $purchaseSum, $quantity, $spk, $inTheHand, 
                                       $riskReserveRate, $k_log, $k_fin, $k_fbr, $k_ps_total, 
                                       $k_mgr, $rate_ndfl, $rate_ins, $k_bonus, $k_spk, $variables);
        } else {
            return $this->calculateOoo($sellingSum, $purchaseSum, $quantity, $spk, $inTheHand, 
                                       $riskReserveRate, $k_log, $k_fin, $k_fbr, $k_ps_total, 
                                       $k_mgr, $rate_ndfl, $rate_ins, $k_bonus, $k_spk, $variables);
        }
    }

    private function calculateInn($sellingSum, $purchaseSum, $quantity, $spk, $inTheHand,
                                   $riskReserveRate, $k_log, $k_fin, $k_fbr, $k_ps_total,
                                   $k_mgr, $rate_ndfl, $rate_ins, $k_bonus, $k_spk, $variables)
    {
        $rate_ausn = (float)($variables['rate_ausn']->value ?? 0.08);

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
        $inTheDeal = ($inTheHand * $k_bonus) + $inTheHand;
        $prfPercent = $sellingSum > 0 ? ($companyProfit / $sellingSum) * 100 : 0;

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
            'prfPercent' => $prfPercent,
            'spk' => $spk,
            'inTheDeal' => $inTheDeal,
        ];
    }

    private function calculateOoo($sellingSum, $purchaseSum, $quantity, $spk, $inTheHand,
                                   $riskReserveRate, $k_log, $k_fin, $k_fbr, $k_ps_total,
                                   $k_mgr, $rate_ndfl, $rate_ins, $k_bonus, $k_spk, $variables)
    {
        $rate_cit = (float)($variables['rate_cit']->value ?? 0.25);

        $ndsOutgoing = $sellingSum / 122 * 22;
        $ndsIncoming = $purchaseSum / 122 * 22;
        $ndsPaid = $ndsOutgoing - $ndsIncoming;

        $nacenka = $sellingSum - $purchaseSum;
        
        $P1 = $nacenka - $ndsPaid;
        
        $riskReserve = max(0, $P1 * $riskReserveRate);
        $premBase = max(0, $P1 - $riskReserve);
        
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

        $citBase = max(0, $P1 - $riskReserve - $premiyaTotal - $totalManagerCost);
        $citTax = $citBase * $rate_cit;

        $totalTaxes = $ndsPaid + $managerNdfl + $socialFunds + $citTax;
        $companyProfit = $citBase - $citTax;
        $inTheDeal = ($inTheHand * $k_bonus) + $inTheHand;
        
        $prfPercent = $sellingSum > 0 ? ($companyProfit / $sellingSum) * 100 : 0;

        return [
            'nacenka' => $nacenka,
            'ndsOutgoing' => $ndsOutgoing,
            'ndsIncoming' => $ndsIncoming,
            'ndsPaid' => $ndsPaid,
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
            'citBase' => $citBase,
            'citTax' => $citTax,
            'totalTaxes' => $totalTaxes,
            'companyProfit' => $companyProfit,
            'prfPercent' => $prfPercent,
            'spk' => $spk,
            'inTheDeal' => $inTheDeal,
        ];
    }

    public function calculate(Request $request)
    {
        $result = $this->calcultating($request);

        $sellingType = $request->input('selling_name');
        $counteragentType = strpos($sellingType, 'ИП') !== false ? 'inn' : 'ooo';

        $calculations = [
            'nacenka' => round($result['nacenka'], 2, PHP_ROUND_HALF_UP),
            'P1' => round($result['P1'], 2, PHP_ROUND_HALF_UP),
            'riskReserve' => round($result['riskReserve'], 2, PHP_ROUND_HALF_UP),
            'premBase' => round($result['premBase'], 2, PHP_ROUND_HALF_UP),
            'logisticsBonus' => round($result['logisticsBonus'], 2, PHP_ROUND_HALF_UP),
            'finAdminBonus' => round($result['finAdminBonus'], 2, PHP_ROUND_HALF_UP),
            'fbrBonus' => round($result['fbrBonus'], 2, PHP_ROUND_HALF_UP),
            'premiyaTotal' => round($result['premiyaTotal'], 2, PHP_ROUND_HALF_UP),
            'managerBase' => round($result['managerBase'], 2, PHP_ROUND_HALF_UP),
            'managerSalaryBrutto' => round($result['managerSalaryBrutto'], 2, PHP_ROUND_HALF_UP),
            'managerNdfl' => round($result['managerNdfl'], 2, PHP_ROUND_HALF_UP),
            'socialFunds' => round($result['socialFunds'], 2, PHP_ROUND_HALF_UP),
            'totalManagerCost' => round($result['totalManagerCost'], 2, PHP_ROUND_HALF_UP),
            'managerPayment' => round($result['managerPayment'], 2, PHP_ROUND_HALF_UP),
            'spkPayment' => round($result['spkPayment'], 2, PHP_ROUND_HALF_UP),
            'perUnitPayment' => round($result['perUnitPayment'], 2, PHP_ROUND_HALF_UP),
            'totalTaxes' => round($result['totalTaxes'], 2, PHP_ROUND_HALF_UP),
            'companyProfit' => round($result['companyProfit'], 2, PHP_ROUND_HALF_UP),
            'prfPercent' => round($result['prfPercent'], 2, PHP_ROUND_HALF_UP),
            'spk' => $result['spk'],
            'inTheDeal' => round($result['inTheDeal'], 2, PHP_ROUND_HALF_UP),
        ];

        if ($counteragentType === 'inn') {
            $calculations['ausn'] = round($result['ausn'], 2, PHP_ROUND_HALF_UP);
        }

        if ($counteragentType === 'ooo') {
            $calculations['ndsOutgoing'] = round($result['ndsOutgoing'], 2, PHP_ROUND_HALF_UP);
            $calculations['ndsIncoming'] = round($result['ndsIncoming'], 2, PHP_ROUND_HALF_UP);
            $calculations['ndsPaid'] = round($result['ndsPaid'], 2, PHP_ROUND_HALF_UP);
            $calculations['citBase'] = round($result['citBase'], 2, PHP_ROUND_HALF_UP);
            $calculations['citTax'] = round($result['citTax'], 2, PHP_ROUND_HALF_UP);
        }

        return response()->json([
            'success' => true,
            'calculations' => $calculations
        ], 201);
    }

    /**
     * Сохранить в отчеты
     * @param Request $request
     */
    public function storeDraftsReport(Request $request)
    {
        return $this->saveReport($request, DraftsReports::class, 'Сохранено в отчёты', true);
    }

    /**
     * Сохранить в историю
     * @param Request $request
     */
    public function storeReport(Request $request)
    {
        return $this->saveReport($request, Reports::class, 'Сохранено в историю', false, true);
    }

    /**
     * Сохранение результатов расчёта
     * @param Request $request
     * @param string $reportModel
     * @param string $message
     * @param bool $includeCalculations
     */
    private function saveReport(Request $request, string $reportModel, string $message, bool $includeCalculations = false, $isHistory = false)
    {
        $calculationId = $this->saveCalculation($request, $isHistory);
        $result = $this->calcultating($request);
        $userName = auth()->user()->name ?? 'Без имени';

        $calculation = Calculation::findOrFail($calculationId);
        $calculation->update([
            'manager_payment' => $result['managerPayment'],
            'manager_salary_brutto' => $result['managerSalaryBrutto'],
            'per_unit_payment' => $result['perUnitPayment'],
            'in_the_deal' => $result['inTheDeal'],
        ]);

        $reportModel::create([
            'manager_id' => auth()->id(),
            'date' => now()->toDateString(),
            'name' => $userName,
            'amount' => $result['managerPayment'],
            'calculate_id' => $calculationId,
        ]);

        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($includeCalculations) {
            $sellingType = $request->input('selling_name');
            $counteragentType = strpos($sellingType, 'ИП') !== false ? 'inn' : 'ooo';

            $response['calculations'] = [
                'nacenka' => round($result['nacenka'], 2, PHP_ROUND_HALF_UP),
                'P1' => round($result['P1'], 2, PHP_ROUND_HALF_UP),
                'riskReserve' => round($result['riskReserve'], 2, PHP_ROUND_HALF_UP),
                'premBase' => round($result['premBase'], 2, PHP_ROUND_HALF_UP),
                'logisticsBonus' => round($result['logisticsBonus'], 2, PHP_ROUND_HALF_UP),
                'finAdminBonus' => round($result['finAdminBonus'], 2, PHP_ROUND_HALF_UP),
                'fbrBonus' => round($result['fbrBonus'], 2, PHP_ROUND_HALF_UP),
                'premiyaTotal' => round($result['premiyaTotal'], 2, PHP_ROUND_HALF_UP),
                'managerBase' => round($result['managerBase'], 2, PHP_ROUND_HALF_UP),
                'managerSalaryBrutto' => round($result['managerSalaryBrutto'], 2, PHP_ROUND_HALF_UP),
                'managerNdfl' => round($result['managerNdfl'], 2, PHP_ROUND_HALF_UP),
                'socialFunds' => round($result['socialFunds'], 2, PHP_ROUND_HALF_UP),
                'totalManagerCost' => round($result['totalManagerCost'], 2, PHP_ROUND_HALF_UP),
                'managerPayment' => round($result['managerPayment'], 2, PHP_ROUND_HALF_UP),
                'spkPayment' => round($result['spkPayment'], 2, PHP_ROUND_HALF_UP),
                'perUnitPayment' => round($result['perUnitPayment'], 2, PHP_ROUND_HALF_UP),
                'totalTaxes' => round($result['totalTaxes'], 2, PHP_ROUND_HALF_UP),
                'companyProfit' => round($result['companyProfit'], 2, PHP_ROUND_HALF_UP),
                'prfPercent' => round($result['prfPercent'], 2, PHP_ROUND_HALF_UP),
            ];

            if ($counteragentType === 'inn') {
                $response['calculations']['ausn'] = round($result['ausn'], 0);
            } else {
                $response['calculations']['ndsOutgoing'] = round($result['ndsOutgoing'], 0);
                $response['calculations']['ndsIncoming'] = round($result['ndsIncoming'], 0);
                $response['calculations']['ndsPaid'] = round($result['ndsPaid'], 0);
                $response['calculations']['citBase'] = round($result['citBase'], 0);
                $response['calculations']['citTax'] = round($result['citTax'], 0);
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
                'date' => 'nullable|string',
                'selling_name' => 'nullable|string',
                'spk' => 'nullable|string',
            ]);
        } else {
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
                'in_the_hand' => 'nullable|numeric',
            ]);
        }

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
