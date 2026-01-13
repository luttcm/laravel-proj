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
        ->sortByDesc('date');

        return view('pages.managers.reports', compact('reports'));
    }

    public function history()
    {
        $reports = Reports::all()
        ->where('manager_id', auth()->id())
        ->sortByDesc('date');

        return view('pages.managers.history', compact('reports'));
    }

    /**
     * Summary of storeReport
     * @param Request $request
     */
    public function storeDraftsReport(Request $request)
    {
        $calculationId = $this->saveCalculation($request);

        $userName = auth()->user()->name ?? 'Без имени';

        DraftsReports::create([
            'manager_id' => auth()->id(),
            'date' => now()->toDateString(),
            'name' => $userName,
            'amount' => 12.0,
            'calculate_id' => $calculationId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Сохранено в отчёты',
        ], 201);
    }

    /**
     * Summary of storeReport
     * @param Request $request
     */
    public function storeReport(Request $request)
    {
        $calculationId = $this->saveCalculation($request);

        $userName = auth()->user()->name ?? 'Без имени';

        Reports::create([
            'manager_id' => auth()->id(),
            'date' => now()->toDateString(),
            'name' => $userName,
            'amount' => 12.0,
            'calculate_id' => $calculationId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Сохранено в отчёты',
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
