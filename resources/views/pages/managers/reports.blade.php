@extends('layouts.app')

@section('title', 'Менеджеры - Отчёты')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="mb-5">
                <h1 style="font-size: 2rem; font-weight: 600; margin-bottom: 8px;">Отчёты</h1>
            </div>

            <div style="display: flex; gap: 16px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #e0e0e0;">
                <a href="{{ route('managers.calculation') }}" style="padding: 8px 16px; background-color: #f0f0f0; color: #333; border-radius: 6px; text-decoration: none; font-weight: 500; transition: all 0.2s; border: 1px solid #e0e0e0;">
                    Расчёт прибыли
                </a>
                <a href="{{ route('managers.reports') }}" style="padding: 8px 16px; background-color: #0084ff; color: white; border-radius: 6px; text-decoration: none; font-weight: 500; transition: all 0.2s;">
                    Отчёты
                </a>
                <a href="{{ route('managers.history') }}" style="padding: 8px 16px; background-color: #f0f0f0; color: #333; border-radius: 6px; text-decoration: none; font-weight: 500; transition: all 0.2s; border: 1px solid #e0e0e0;">
                    История расчётов
                </a>
            </div>

            <div style="background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 32px;">
               <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Дата</th>
                            <th>Название отчета</th>
                            <th>Сумма рассчетов</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td>{{ $report->date }}</td>
                                <td>{{ $report->report_title }}</td>
                                <td>{{ $report->amount }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Нет данных
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    a:hover {
        opacity: 0.9;
    }
</style>
@endsection
