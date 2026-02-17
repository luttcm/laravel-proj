<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DraftsReports;
use App\Models\Reports;
use App\Models\FinReport;
use App\Services\Calculation\DTO\FinDirectorCalculationRequestDTO;
use App\Services\Calculation\Strategies\FinDirectorCalculationStrategy;

class FinDirectorController extends ManagersController
{
    public function calculation()
    {
        $spks = \App\Models\Spk::all();
        $suppliers = \App\Models\Supplier::all();
        return view('pages.findirector.calculation', compact('spks', 'suppliers'));
    }

    public function reports()
    {
        $reports = DraftsReports::all()
        ->where('manager_id', auth()->id())
        ->sortByDesc('created_at');

        return view('pages.findirector.reports', compact('reports'));
    }

    public function history()
    {
        $reports = Reports::all()
        ->where('manager_id', auth()->id())
        ->sortByDesc('created_at');

        return view('pages.findirector.history', compact('reports'));
    }

    public function finReportsIndex()
    {
        $reports = FinReport::with(['spkPerson', 'supplier', 'nds'])
            ->where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('pages.findirector.fin_reports.index', compact('reports'));
    }

    public function finReportsAdd()
    {
        $spks = \App\Models\Spk::all();
        $suppliers = \App\Models\Supplier::all();
        $nds = \App\Models\Nds::all();
        return view('pages.findirector.fin_reports.add', compact('spks', 'suppliers', 'nds'));
    }

    public function finReportsStore(Request $request)
    {
        $validated = $request->validate([
            'report_title' => 'required|string|max:255',
            'customer' => 'nullable|string|max:255',
            'order_number' => 'nullable|string|max:255',
            'spk' => 'nullable|string|max:255',
            'spk_id' => 'nullable|exists:spks,id',
            'tz_count' => 'nullable|integer',
            'amount' => 'required|numeric',
            'received_amount' => 'nullable|numeric',
            'date' => 'nullable|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'nds_id' => 'nullable|exists:nds,id',
            'bonus_client' => 'nullable|numeric',
            'net_sales' => 'nullable|numeric',
            'remainder' => 'nullable|numeric',
            'manager_name' => 'nullable|string|max:255',
            'supplier_invoice_number' => 'nullable|string|max:255',
            'supplier_amount' => 'nullable|numeric',
            'payment_manager' => 'nullable|numeric',
            'payment_spk' => 'nullable|numeric',
            'sold_from' => 'nullable|string|max:255',
            'profit' => 'nullable|numeric',
            'markup' => 'nullable|numeric',
            'nds_percent' => 'nullable|numeric',
        ]);

        $data = $validated;
        $data['user_id'] = auth()->id();
        $data['received_amount'] = $validated['received_amount'] ?? 0;
        $data['date'] = $validated['date'] ?? now()->toDateString();
        $data['bonus_client'] = $validated['bonus_client'] ?? 0;
        $data['kickback'] = $validated['kickback'] ?? 0;
        $data['net_sales'] = $validated['net_sales'] ?? 0;
        $data['remainder'] = $validated['remainder'] ?? 0;
        $data['supplier_amount'] = $validated['supplier_amount'] ?? 0;
        $data['payment_manager'] = $validated['payment_manager'] ?? 0;
        $data['payment_spk'] = $validated['payment_spk'] ?? 0;
        $data['profit'] = $validated['profit'] ?? 0;
        $data['markup'] = $validated['markup'] ?? 0;
        $data['nds_percent'] = $validated['nds_percent'] ?? 0;
        
        $calcRequest = FinDirectorCalculationRequestDTO::fromRequest($request);
        $calcStrategy = new FinDirectorCalculationStrategy();
        $calcResult = $calcStrategy->calculate($calcRequest);
        
        $data['remainder'] = $calcResult->remainder;
        $data['net_sales'] = $calcResult->netSales;
        $data['payment_manager'] = $calcResult->paymentManager;
        $data['payment_spk'] = $calcResult->paymentSpk;
        $data['profit'] = $calcResult->profit;
        $data['markup'] = $calcResult->markup;

        FinReport::create($data);

        return redirect()->route('findirector.fin-reports.index')
            ->with('success', 'Отчет успешно создан');
    }

    public function finReportsEdit($id)
    {
        $report = FinReport::findOrFail($id);

        if ($report->user_id !== auth()->id()) {
            abort(403);
        }

        $spks = \App\Models\Spk::all();
        $suppliers = \App\Models\Supplier::all();
        $nds = \App\Models\Nds::all();
        return view('pages.findirector.fin_reports.edit', compact('report', 'spks', 'suppliers', 'nds'));
    }

    public function finReportsUpdate(Request $request, $id)
    {
        $report = \App\Models\FinReport::findOrFail($id);

        if ($report->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'report_title' => 'required|string|max:255',
            'customer' => 'nullable|string|max:255',
            'order_number' => 'nullable|string|max:255',
            'spk' => 'nullable|string|max:255',
            'spk_id' => 'nullable|exists:spks,id',
            'tz_count' => 'nullable|integer', 
            'amount' => 'required|numeric',
            'received_amount' => 'nullable|numeric',
            'date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'nds_id' => 'nullable|exists:nds,id',
            'bonus_client' => 'nullable|numeric',
            'net_sales' => 'nullable|numeric',
            'remainder' => 'nullable|numeric',
            'manager_name' => 'nullable|string|max:255',
            'supplier_invoice_number' => 'nullable|string|max:255',
            'supplier_amount' => 'nullable|numeric',
            'payment_manager' => 'nullable|numeric',
            'payment_spk' => 'nullable|numeric',
            'sold_from' => 'nullable|string|max:255',
            'profit' => 'nullable|numeric',
            'markup' => 'nullable|numeric',
            'nds_percent' => 'nullable|numeric',
        ]);

        $data = $validated;
        
        $calcRequest = FinDirectorCalculationRequestDTO::fromRequest($request);
        $calcStrategy = new FinDirectorCalculationStrategy();
        $calcResult = $calcStrategy->calculate($calcRequest);
        
        $data['remainder'] = $calcResult->remainder;
        $data['net_sales'] = $calcResult->netSales;
        $data['payment_manager'] = $calcResult->paymentManager;
        $data['payment_spk'] = $calcResult->paymentSpk;
        $data['profit'] = $calcResult->profit;
        $data['markup'] = $calcResult->markup;

        $report->update($data);

        return redirect()->route('findirector.fin-reports.index')
            ->with('success', 'Отчет успешно обновлен');
    }

    public function finReportsDelete($id)
    {
        $report = FinReport::findOrFail($id);

        if ($report->user_id !== auth()->id()) {
            abort(403);
        }

        $report->delete();

        return redirect()->route('findirector.fin-reports.index')
            ->with('success', 'Отчет удален');
    }
}
