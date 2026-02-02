@extends('layouts.app')

@section('title', 'Финансовый директор - Отчёты (ручные)')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 600; margin-bottom: 8px;">Отчёты (ручной ввод)</h1>
                    <p class="text-muted">Список отчетов, введенных вручную</p>
                </div>
            <div style="display: flex; gap: 16px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #e0e0e0;">
                <a href="{{ route('findirector.calculation') }}" style="padding: 8px 16px; background-color: #f0f0f0; color: #333; border-radius: 6px; text-decoration: none; font-weight: 500; transition: all 0.2s; border: 1px solid #e0e0e0;">
                    Расчёт прибыли
                </a>
                <a href="{{ route('findirector.reports') }}" style="padding: 8px 16px; background-color: #f0f0f0; color: #333; border-radius: 6px; text-decoration: none; font-weight: 500; transition: all 0.2s; border: 1px solid #e0e0e0;">
                    Отчёты
                </a>
                <a href="{{ route('findirector.history') }}" style="padding: 8px 16px; background-color: #f0f0f0; color: #333; border-radius: 6px; text-decoration: none; font-weight: 500; transition: all 0.2s; border: 1px solid #e0e0e0;">
                    История расчётов
                </a>
                <a href="{{ route('findirector.fin-reports.index') }}" style="padding: 8px 16px; background-color: #0084ff; color: white; border-radius: 6px; text-decoration: none; font-weight: 500; transition: all 0.2s;">
                    Отчёты (ручной ввод)
                </a>
            </div>

            <div class="mb-4">
                <a href="{{ route('findirector.fin-reports.add') }}" class="btn btn-primary" style="padding: 10px 20px; border-radius: 6px; font-weight: 500;">
                    Добавить отчет вручную
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div style="background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 32px;">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Дата</th>
                            <th>Название отчета</th>
                            <th>Итоговая сумма</th>
                            <th class="text-end">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($report->date)->format('d.m.Y') }}</td>
                                <td>{{ $report->report_title }}</td>
                                <td style="font-weight: 600;">{{ number_format($report->amount, 2, '.', ' ') }} ₽</td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('findirector.fin-reports.edit', $report->id) }}" class="btn btn-sm btn-outline-secondary">
                                            Редактировать
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $report->id }})">
                                            Удалить
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $report->id }}" action="{{ route('findirector.fin-reports.delete', $report->id) }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    Нет данных. Нажмите "Добавить отчет", чтобы создать первую запись.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                 <a href="{{ route('findirector.reports') }}" class="btn btn-outline-primary">Вернуться к калькулятору</a>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Вы уверены, что хотите удалить этот отчет?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endsection
