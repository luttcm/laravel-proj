@extends('layouts.app')

@section('title', 'Финансовый директор - Отчёты (ручные)')
@section('container_class', 'container-fluid')

@section('content')
<div class="px-4 my-5">
    <div class="row">
        <div class="col-12">
            <div class="mb-5">
                <h1 style="font-size: 2rem; font-weight: 600; margin-bottom: 8px;">Отчёты Финансового директора</h1>
            </div>

            <div class="mb-4 d-flex justify-content-end">
                <a href="{{ route('findirector.fin-reports.add') }}" class="btn btn-primary" style="padding: 10px 20px; border-radius: 6px; font-weight: 500;">
                    Добавить
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div style="background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 24px; overflow-x: auto;">
                <table class="table table-hover" style="min-width: 2000px;">
                    <thead class="table-light">
                        <tr>
                            <th style="white-space: nowrap;">Дата</th>
                            <th style="white-space: nowrap;">Заказчик</th>
                            <th style="white-space: nowrap;">Номер заказа</th>
                            <th style="white-space: nowrap;">СПК</th>
                            <th style="white-space: nowrap;">Кол-во ТЗ</th>
                            <th style="white-space: nowrap;">Сумма счета</th>
                            <th style="white-space: nowrap;">Поступило Фактически</th>
                            <th style="white-space: nowrap;">Остаток по оплате</th>
                            <th style="white-space: nowrap;">Бонус клиенту</th>
                            <th style="white-space: nowrap;">Чистая продажа</th>
                            <th style="white-space: nowrap;">Поставщик</th>
                            <th style="white-space: nowrap;">№ счета Поставщика</th>
                            <th style="white-space: nowrap;">Сумма Поставщика</th>
                            <th style="white-space: nowrap;">Менеджер</th>
                            <th style="white-space: nowrap;">Выплата Менеджеру (на руки)</th>
                            <th style="white-space: nowrap;">Выплата СПК (на руки)</th>
                            <th style="white-space: nowrap;">Наценка</th>
                            <th style="white-space: nowrap;">PRF</th>
                            <th style="white-space: nowrap;">FAB (на руки)</th>
                            <th style="white-space: nowrap;">LGB (на руки)</th>
                            <th style="white-space: nowrap;">FBR (на руки)</th>

                            <th class="text-end">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                             <tr>
                                <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($report->date)->format('d.m.Y') }}</td>
                                <td>{{ $report->customer }}</td>
                                <td>{{ $report->order_number }}</td>
                                <td>{{ $report->spk_id ? ($report->spkPerson->fio ?? 'Неизвестно') : ($report->spk ?: 'Без СПК') }}</td>
                                <td>{{ $report->tz_count ?? 0 }}</td>
                                <td style="font-weight: 600; white-space: nowrap;">{{ number_format($report->amount, 0, '.', ' ') }}</td>
                                <td style="font-weight: 600; white-space: nowrap;">{{ number_format($report->received_amount, 0, '.', ' ') }}</td>
                                <td style="white-space: nowrap;">{{ number_format($report->remainder, 0, '.', ' ') }}</td>
                                <td style="white-space: nowrap;">{{ number_format($report->bonus_client, 0, '.', ' ') }}</td>
                                <td style="white-space: nowrap;">{{ number_format($report->net_sales, 0, '.', ' ') }}</td>
                                <td>{{ $report->supplier->name ?? '—' }}</td>
                                <td>{{ $report->supplier_invoice_number ?? '—' }}</td>
                                <td style="white-space: nowrap;">{{ number_format($report->supplier_amount, 0, '.', ' ') }}</td>
                                <td>{{ $report->manager_name ?? '—' }}</td>
                                <td style="white-space: nowrap;">{{ number_format($report->payment_manager, 0, '.', ' ') }}</td>
                                <td style="white-space: nowrap;">{{ number_format($report->payment_spk, 0, '.', ' ') }}</td>
                                <td style="white-space: nowrap;">{{ (float)$report->markup }}%</td>
                                <td style="font-weight: 600; white-space: nowrap;">{{ number_format($report->profit, 0, '.', ' ') }}</td>
                                <td style="white-space: nowrap;">{{ number_format($report->fin_admin_bonus, 0, '.', ' ') }}</td>
                                <td style="white-space: nowrap;">{{ number_format($report->logistics_bonus, 0, '.', ' ') }}</td>
                                <td style="white-space: nowrap;">{{ number_format($report->fbr_bonus, 0, '.', ' ') }}</td>
                                
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('findirector.fin-reports.edit', $report->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $report->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $report->id }}" action="{{ route('findirector.fin-reports.delete', $report->id) }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="24" class="text-center text-muted py-5">
                                    Нет данных. Нажмите "Добавить", чтобы создать первую запись.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light mt-3" style="font-weight: 600; border-top: 2px solid #ccc;">
                        @php
                            $sumTzCount = $reports->sum('tz_count');
                            $sumAmount = $reports->sum('amount');
                            $sumReceived = $reports->sum('received_amount');
                            $sumRemainder = $reports->sum('remainder');
                            $sumBonusClient = $reports->sum('bonus_client');
                            $sumNetSales = $reports->sum('net_sales');
                            $sumSupplierAmount = $reports->sum('supplier_amount');
                            $sumPaymentManager = $reports->sum('payment_manager');
                            $sumPaymentSpk = $reports->sum('payment_spk');
                            $sumProfit = $reports->sum('profit');
                            $sumFinAdmin = $reports->sum('fin_admin_bonus');
                            $sumLogistics = $reports->sum('logistics_bonus');
                            $sumFbr = $reports->sum('fbr_bonus');
                        @endphp
                        <tr>
                        
                            <td colspan="4" class="text-end"></td>
                            <td style="white-space: nowrap;">{{ $sumTzCount > 0 ? $sumTzCount : '0' }}</td>
                            <td style="white-space: nowrap;">{{ number_format($sumAmount, 0, '.', ' ') }}</td>
                            <td style="white-space: nowrap;">{{ number_format($sumReceived, 0, '.', ' ') }}</td>
                            <td style="white-space: nowrap;">{{ number_format($sumRemainder, 0, '.', ' ') }}</td>
                            <td style="white-space: nowrap;">{{ number_format($sumBonusClient, 0, '.', ' ') }}</td>
                            <td style="white-space: nowrap;">{{ number_format($sumNetSales, 0, '.', ' ') }}</td>
                            <td colspan="2"></td>
                            <td style="white-space: nowrap;">{{ number_format($sumSupplierAmount, 0, '.', ' ') }}</td>
                            <td></td>
                            <td style="white-space: nowrap;">{{ number_format($sumPaymentManager, 0, '.', ' ') }}</td>
                            <td style="white-space: nowrap;">{{ number_format($sumPaymentSpk, 0, '.', ' ') }}</td>
                            <td></td>
                            <td style="white-space: nowrap;">{{ number_format($sumProfit, 0, '.', ' ') }}</td>
                            <td style="white-space: nowrap;">{{ number_format($sumFinAdmin, 0, '.', ' ') }}</td>
                            <td style="white-space: nowrap;">{{ number_format($sumLogistics, 0, '.', ' ') }}</td>
                            <td style="white-space: nowrap;">{{ number_format($sumFbr, 0, '.', ' ') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="mt-4">
                    {{ $reports->links() }}
                </div>
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
