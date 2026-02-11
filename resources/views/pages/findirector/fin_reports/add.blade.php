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

                        <div class="col-md-12 mb-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Дата (по умолчанию сегодня)</label>
                            <input type="date" class="form-control" name="date" value="{{ old('date', date('Y-m-d')) }}" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
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
@endsection
