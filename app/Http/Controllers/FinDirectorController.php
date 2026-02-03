<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DraftsReports;
use App\Models\Reports;
use App\Models\FinReport;

class FinDirectorController extends ManagersController
{
    public function calculation()
    {
        return view('pages.findirector.calculation');
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

    // Ручные отчеты для финдиректора
    public function finReportsIndex()
    {
        $reports = FinReport::where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pages.findirector.fin_reports.index', compact('reports'));
    }

    public function finReportsAdd()
    {
        return view('pages.findirector.fin_reports.add');
    }

    public function finReportsStore(Request $request)
    {
        $validated = $request->validate([
            'report_title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'nullable|date',
        ]);

        FinReport::create([
            'user_id' => auth()->id(),
            'report_title' => $validated['report_title'],
            'amount' => $validated['amount'],
            'date' => $validated['date'] ?? now()->toDateString(),
        ]);

        return redirect()->route('findirector.fin-reports.index')
            ->with('success', 'Отчет успешно создан');
    }

    public function finReportsEdit($id)
    {
        $report = FinReport::findOrFail($id);
        
        if ($report->user_id !== auth()->id()) {
            abort(403);
        }

        return view('pages.findirector.fin_reports.edit', compact('report'));
    }

    public function finReportsUpdate(Request $request, $id)
    {
        $report = \App\Models\FinReport::findOrFail($id);
        
        if ($report->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'report_title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $report->update($validated);

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
