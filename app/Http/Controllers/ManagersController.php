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
    private function calcultating(Request $request)
    {
        $sellingType = $request->input('selling_name');
        $spk = $request->input('spk');
        $inTheHand = $request->input('in_the_hand');
        $ndsPercentPurchase = (float)$request->input('nds_percent', 0);

        $counteragentType = strpos($sellingType, 'ИП') !== false ? 'inn' : 'ooo';

        if ($counteragentType === 'inn') {
            $ndsPercentPurchase = 0;
            $ndsPercentSelling = 0;
        } else {
            $standardNds = \App\Models\Nds::where('code_name', 'nds_standart')->first();
            $ndsPercentSelling = $standardNds ? (float)$standardNds->percent : 22;
        }

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
                                       $k_mgr, $rate_ndfl, $rate_ins, $k_bonus, $k_spk, $variables,
                                       $ndsPercentPurchase, $ndsPercentSelling);
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
        $premiyaTotal = $logisticsBonus + $finAdminBonus + $fbrBonus;

        $managerBase = max(0, $premBase - $premiyaTotal);
        $managerSalaryBrutto = $managerBase * $k_mgr;
        $managerNdfl = $managerSalaryBrutto * $rate_ndfl;

        $socialFunds = $managerSalaryBrutto * $rate_ins;
        $percentSumm = $premiyaTotal * $rate_ins;

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
        $companyProfit = $P1 - $riskReserve - $premiyaTotal - $managerSalaryBrutto - $socialFunds - $percentSumm;
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
                                   $k_mgr, $rate_ndfl, $rate_ins, $k_bonus, $k_spk, $variables,
                                   $ndsPercentPurchase = 0, $ndsPercentSelling = 18)
    {
        $rate_cit = (float)($variables['rate_cit']->value ?? 0.25);

        if ($ndsPercentPurchase > 0) {
            $ndsIncoming = $purchaseSum / (100 + $ndsPercentPurchase) * $ndsPercentPurchase;
        } else {
            $ndsIncoming = 0;
        }

        $ndsOutgoing = $sellingSum / (100 + $ndsPercentSelling) * $ndsPercentSelling;
        $ndsPaid = $ndsOutgoing - $ndsIncoming;

        $nacenka = $sellingSum - $purchaseSum;
        
        $P1 = $nacenka - $ndsPaid;
        
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
        $percentSumm = $premiyaTotal * $rate_ins;
        
        $managerPayment = $managerSalaryBrutto - $managerNdfl;       
        $totalManagerCost = $managerSalaryBrutto + $socialFunds;
        $perUnitPayment = $quantity > 0 ? $managerPayment / $quantity : 0;

        $spkPayment = 0;
        if ($spk == 'Y') {
            $spkPayment = $managerPayment * $k_spk;
            $managerPayment -= $spkPayment;
            $perUnitPayment = $quantity > 0 ? $managerPayment / $quantity : 0;
        }

        $citBase = max(0, $P1 - $riskReserve - $premiyaTotal - $totalManagerCost - $percentSumm);
        $citTax = $citBase * $rate_cit;

        $totalTaxes = $ndsPaid + $managerNdfl + $socialFunds + $citTax;
        $companyProfit = $P1 - $riskReserve - $premiyaTotal - $managerSalaryBrutto - $socialFunds - $percentSumm - $citTax;
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
            'nacenka' => round($result['nacenka'], 0, PHP_ROUND_HALF_UP),
            'P1' => round($result['P1'], 0, PHP_ROUND_HALF_UP),
            'riskReserve' => round($result['riskReserve'], 0, PHP_ROUND_HALF_UP),
            'premBase' => round($result['premBase'], 0, PHP_ROUND_HALF_UP),
            'logisticsBonus' => round($result['logisticsBonus'], 0, PHP_ROUND_HALF_UP),
            'finAdminBonus' => round($result['finAdminBonus'], 0, PHP_ROUND_HALF_UP),
            'fbrBonus' => round($result['fbrBonus'], 0, PHP_ROUND_HALF_UP),
            'premiyaTotal' => round($result['premiyaTotal'], 0, PHP_ROUND_HALF_UP),
            'managerBase' => round($result['managerBase'], 0, PHP_ROUND_HALF_UP),
            'managerSalaryBrutto' => round($result['managerSalaryBrutto'], 0, PHP_ROUND_HALF_UP),
            'managerNdfl' => round($result['managerNdfl'], 0, PHP_ROUND_HALF_UP),
            'socialFunds' => round($result['socialFunds'], 0, PHP_ROUND_HALF_UP),
            'totalManagerCost' => round($result['totalManagerCost'], 0, PHP_ROUND_HALF_UP),
            'managerPayment' => round($result['managerPayment'], 0, PHP_ROUND_HALF_UP),
            'spkPayment' => round($result['spkPayment'], 0, PHP_ROUND_HALF_UP),
            'perUnitPayment' => round($result['perUnitPayment'], 0, PHP_ROUND_HALF_UP),
            'totalTaxes' => round($result['totalTaxes'], 0, PHP_ROUND_HALF_UP),
            'companyProfit' => round($result['companyProfit'], 0, PHP_ROUND_HALF_UP),
            'prfPercent' => round($result['prfPercent'], 2, PHP_ROUND_HALF_UP),
            'spk' => $result['spk'],
            'inTheDeal' => round($result['inTheDeal'], 0, PHP_ROUND_HALF_UP),
        ];

        if ($counteragentType === 'inn') {
            $calculations['ausn'] = round($result['ausn'], 0, PHP_ROUND_HALF_UP);
        }

        if ($counteragentType === 'ooo') {
            $calculations['ndsOutgoing'] = round($result['ndsOutgoing'], 0, PHP_ROUND_HALF_UP);
            $calculations['ndsIncoming'] = round($result['ndsIncoming'], 0, PHP_ROUND_HALF_UP);
            $calculations['ndsPaid'] = round($result['ndsPaid'], 0, PHP_ROUND_HALF_UP);
            $calculations['citBase'] = round($result['citBase'], 0, PHP_ROUND_HALF_UP);
            $calculations['citTax'] = round($result['citTax'], 0, PHP_ROUND_HALF_UP);
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
        $calculationId = $request->query('calculation_id');
        return $this->saveReport($request, DraftsReports::class, 'Сохранено в отчёты', true, false, $calculationId);
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
    private function saveReport(Request $request, string $reportModel, string $message, bool $includeCalculations = false, $isHistory = false, $existingCalculationId = null)
    {
        if ($existingCalculationId) {
            $calculationId = $existingCalculationId;
            $calculation = Calculation::findOrFail($calculationId);
            $calculation->update($request->all());
        } else {
            $calculationId = $this->saveCalculation($request, $isHistory);
        }

        $reportId = null;
        if ($request->has('report_id') && !empty($request->input('report_id'))) {
            $reportId = $reportModel::findOrFail($request->input('report_id'));
        }
        
        $result = $this->calcultating($request);
        $userName = auth()->user()->name ?? 'Без имени';

        $calculation = Calculation::findOrFail($calculationId);
        $calculation->update([
            'manager_payment' => $result['managerPayment'],
            'manager_salary_brutto' => $result['managerSalaryBrutto'],
            'per_unit_payment' => $result['perUnitPayment'],
            'in_the_deal' => $result['inTheDeal'],
        ]);

        if ($existingCalculationId) {
            if ($reportId) {
                $reportId->update([
                    'date' => now()->toDateString(),
                    'report_title' => $request->input('report_name') ?: 'Отчет ' . now()->format('d.m.Y H:i'),
                    'amount' => $result['managerPayment'],
                ]);
            } else {
                $reportModel::create([
                    'manager_id' => auth()->id(),
                    'date' => now()->toDateString(),
                    'name' => $userName,
                    'report_title' => $request->input('report_name') ?: 'Отчет ' . now()->format('d.m.Y H:i'),
                    'amount' => $result['managerPayment'],
                    'calculate_id' => $calculationId,
                ]);
            }
        } elseif ($reportId) {
            $reportId->update([
                'date' => now()->toDateString(),
                'report_title' => $request->input('report_name') ?: 'Отчет ' . now()->format('d.m.Y H:i'),
                'amount' => $result['managerPayment'],
            ]);
        } else {
            $reportModel::create([
                'manager_id' => auth()->id(),
                'date' => now()->toDateString(),
                'name' => $userName,
                'report_title' => $request->input('report_name') ?: 'Отчет ' . now()->format('d.m.Y H:i'),
                'amount' => $result['managerPayment'],
                'calculate_id' => $calculationId,
            ]);
        }

        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($includeCalculations) {
            $sellingType = $request->input('selling_name');
            $counteragentType = strpos($sellingType, 'ИП') !== false ? 'inn' : 'ooo';

            $response['calculations'] = [
                'nacenka' => round($result['nacenka'], 0, PHP_ROUND_HALF_UP),
                'P1' => round($result['P1'], 0, PHP_ROUND_HALF_UP),
                'riskReserve' => round($result['riskReserve'], 0, PHP_ROUND_HALF_UP),
                'premBase' => round($result['premBase'], 0, PHP_ROUND_HALF_UP),
                'logisticsBonus' => round($result['logisticsBonus'], 0, PHP_ROUND_HALF_UP),
                'finAdminBonus' => round($result['finAdminBonus'], 0, PHP_ROUND_HALF_UP),
                'fbrBonus' => round($result['fbrBonus'], 0, PHP_ROUND_HALF_UP),
                'premiyaTotal' => round($result['premiyaTotal'], 0, PHP_ROUND_HALF_UP),
                'managerBase' => round($result['managerBase'], 0, PHP_ROUND_HALF_UP),
                'managerSalaryBrutto' => round($result['managerSalaryBrutto'], 0, PHP_ROUND_HALF_UP),
                'managerNdfl' => round($result['managerNdfl'], 0, PHP_ROUND_HALF_UP),
                'socialFunds' => round($result['socialFunds'], 0, PHP_ROUND_HALF_UP),
                'totalManagerCost' => round($result['totalManagerCost'], 0, PHP_ROUND_HALF_UP),
                'managerPayment' => round($result['managerPayment'], 0, PHP_ROUND_HALF_UP),
                'spkPayment' => round($result['spkPayment'], 0, PHP_ROUND_HALF_UP),
                'perUnitPayment' => round($result['perUnitPayment'], 0, PHP_ROUND_HALF_UP),
                'totalTaxes' => round($result['totalTaxes'], 0, PHP_ROUND_HALF_UP),
                'companyProfit' => round($result['companyProfit'], 0, PHP_ROUND_HALF_UP),
                'prfPercent' => round($result['prfPercent'], 0, PHP_ROUND_HALF_UP),
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

