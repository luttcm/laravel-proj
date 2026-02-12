@extends('layouts.app')

@section('title', 'Финансовый директор - Добавить отчет')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="mb-5">
                <h1 style="font-size: 2rem; font-weight: 600; margin-bottom: 8px;">Добавить отчет</h1>
                <p class="text-muted">Введите данные для нового ручного отчета</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 32px;">
                <form action="{{ route('findirector.fin-reports.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Название отчета</label>
                            <input type="text" class="form-control" name="report_title" value="{{ old('report_title') }}" required style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="Введите название">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Заказчик</label>
                            <input type="text" class="form-control" name="customer" value="{{ old('customer') }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Номер заказа</label>
                            <input type="text" class="form-control" name="order_number" value="{{ old('order_number') }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">СПК</label>
                            <select class="form-control" name="spk_id" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                                <option value="">Без СПК</option>
                                @foreach($spks as $spk)
                                    <option value="{{ $spk->id }}" {{ old('spk_id') == $spk->id ? 'selected' : '' }}>{{ $spk->fio }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Количество ТЗ</label>
                            <input type="number" class="form-control" name="tz_count" value="{{ old('tz_count') }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Итоговая сумма</label>
                            <input type="number" step="0.01" class="form-control" name="amount" value="{{ old('amount') }}" required style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="0.00">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Фактически поступило</label>
                            <input type="number" step="0.01" class="form-control" name="received_amount" value="{{ old('received_amount') }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="0.00">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Дата (по умолчанию сегодня)</label>
                            <input type="date" class="form-control" name="date" value="{{ old('date', date('Y-m-d')) }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Поставщик</label>
                            <select class="form-control" id="supplier_id" name="supplier_id" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                                <option value="">Выбрать поставщика</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" data-vat="{{ (float)$supplier->vat }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }} ({{ (float)$supplier->vat }}%)</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">НДС (%)</label>
                            <input type="number" step="0.01" class="form-control" id="nds_percent" name="nds_percent" value="{{ old('nds_percent', 0) }}" readonly style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px; background-color: #f9f9f9;">
                            <input type="hidden" name="nds_id" id="nds_id_hidden">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Бонус клиенту</label>
                            <input type="number" step="0.01" class="form-control" name="bonus_client" value="{{ old('bonus_client', 0) }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Чистая продажа</label>
                            <input type="number" step="0.01" class="form-control" name="net_sales" value="{{ old('net_sales', 0) }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Остаток, руб.</label>
                            <input type="number" step="0.01" class="form-control" name="remainder" value="{{ old('remainder', 0) }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Менеджер</label>
                            <input type="text" class="form-control" name="manager_name" value="{{ old('manager_name') }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="ФИО менеджера">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">№ счета от ПОСТАВЩИКА</label>
                            <input type="text" class="form-control" name="supplier_invoice_number" value="{{ old('supplier_invoice_number') }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Сумма счета от поставщика</label>
                            <input type="number" step="0.01" class="form-control" name="supplier_amount" value="{{ old('supplier_amount', 0) }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Выплата менеджеру</label>
                            <input type="number" step="0.01" class="form-control" name="payment_manager" value="{{ old('payment_manager', 0) }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Выплата СПК</label>
                            <input type="number" step="0.01" class="form-control" name="payment_spk" value="{{ old('payment_spk', 0) }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">ОТ КОГО продано</label>
                            <input type="text" class="form-control" name="sold_from" value="{{ old('sold_from') }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">PRF (Профит)</label>
                            <input type="number" step="0.01" class="form-control" name="profit" value="{{ old('profit', 0) }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Наценка на цену завода (%)</label>
                            <input type="number" step="0.01" class="form-control" name="markup" value="{{ old('markup', 0) }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e0e0e0;">
                        <button type="submit" class="btn btn-primary" style="flex: 1; padding: 10px 16px; font-weight: 500;">
                            Сохранить
                        </button>
                        <a href="{{ route('findirector.fin-reports.index') }}" class="btn btn-outline-secondary" style="flex: 1; padding: 10px 16px; font-weight: 500;">
                            Отмена
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const supplierSelect = document.getElementById('supplier_id');
    const ndsPercentInput = document.getElementById('nds_percent');
    
    const amountInput = document.querySelector('input[name="amount"]');
    const receivedAmountInput = document.querySelector('input[name="received_amount"]');
    const bonusClientInput = document.querySelector('input[name="bonus_client"]');
    const netSalesInput = document.querySelector('input[name="net_sales"]');
    const remainderInput = document.querySelector('input[name="remainder"]');

    function calculateFinFields() {
        const amount = parseFloat(amountInput.value) || 0;
        const receivedAmount = parseFloat(receivedAmountInput.value) || 0;
        const bonusClient = parseFloat(bonusClientInput.value) || 0;

        // остаток руб = Сумма счета для КЛИЕНТА - Поступило реально, руб.
        const remainder = amount - receivedAmount;
        remainderInput.value = remainder.toFixed(2);

        // Чистая продажа РУБ. = Сумма счета для КЛИЕНТА - ( Поступило реально, руб. - БОНУС КЛИЕНТУ)
        const netSales = amount - (receivedAmount - bonusClient);
        netSalesInput.value = netSales.toFixed(2);
    }

    if (amountInput) amountInput.addEventListener('input', calculateFinFields);
    if (receivedAmountInput) receivedAmountInput.addEventListener('input', calculateFinFields);
    if (bonusClientInput) bonusClientInput.addEventListener('input', calculateFinFields);

    // Initial calculation
    calculateFinFields();

    if (supplierSelect && ndsPercentInput) {
        supplierSelect.addEventListener('change', function() {
            const selectedOption = supplierSelect.options[supplierSelect.selectedIndex];
            const supplierVat = selectedOption.getAttribute('data-vat');
            
            console.log('Selected Supplier VAT:', supplierVat);

            if (supplierVat !== null && supplierVat !== '') {
                ndsPercentInput.value = parseFloat(supplierVat);
            } else {
                ndsPercentInput.value = 0;
            }
        });
    }
});
</script>
@endsection
@endsection
