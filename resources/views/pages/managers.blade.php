@extends('layouts.app')

@section('title', 'Менеджеры - Форма расчёта')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="mb-5">
                <h1 style="font-size: 2rem; font-weight: 600; margin-bottom: 8px;">Форма расчёта</h1>
            </div>

            <div style="background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 32px;">
                <form id="calculationForm">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Покупаю</label>
                            <input type="text" class="form-control" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="Название товара">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Продаю</label>
                            <input type="text" class="form-control" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="Название товара">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">СПК</label>
                            <input type="text" class="form-control" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="СПК">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Цена покупки, р.</label>
                            <input type="number" class="form-control" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="0.00" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Кол-во изделий, шт.</label>
                            <input type="number" class="form-control" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="0" step="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Сумма покупки, руб.</label>
                            <input type="number" class="form-control" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px; background-color: #f9f9f9;" placeholder="0.00" readonly>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Наценка, %</label>
                            <input type="number" class="form-control" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="0.00" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Цена продажи, руб.</label>
                            <input type="number" class="form-control" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px; background-color: #f9f9f9;" placeholder="0.00" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Сумма продажи, руб.</label>
                            <input type="number" class="form-control" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px; background-color: #f9f9f9;" placeholder="0.00" readonly>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">PRF, %</label>
                            <input type="number" class="form-control" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="0.00" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Выплата по сделке, руб.</label>
                            <input type="number" class="form-control" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px; background-color: #f9f9f9;" placeholder="0.00" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Выплата с 1 шт., руб.</label>
                            <input type="number" class="form-control" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px; background-color: #f9f9f9;" placeholder="0.00" readonly>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e0e0e0;">
                        <button type="button" class="btn" style="flex: 1; background-color: #e8d5f2; color: #6c3fa0; border: none; border-radius: 6px; padding: 10px 16px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                            Рассчитать
                        </button>
                        <button type="button" class="btn" style="flex: 1; background-color: #d5e8f2; color: #3f6ca0; border: none; border-radius: 6px; padding: 10px 16px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                            Сохранить в отчёт
                        </button>
                        <button type="button" class="btn" style="flex: 1; background-color: #e8f2d5; color: #6ca03f; border: none; border-radius: 6px; padding: 10px 16px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                            Сохранить в историю
                        </button>
                        <button type="button" class="btn" style="flex: 1; background-color: #f2d5d5; color: #a03f3f; border: none; border-radius: 6px; padding: 10px 16px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                            Очистить всё
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .btn:hover {
        opacity: 0.85;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .btn:active {
        transform: translateY(0);
    }

    .form-control:focus {
        border-color: #0084ff !important;
        box-shadow: 0 0 0 3px rgba(0, 132, 255, 0.1);
    }
</style>
@endsection
