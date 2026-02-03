@extends('layouts.app')

@section('title', 'Менеджеры - Форма расчёта')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="mb-5">
                <h1 style="font-size: 2rem; font-weight: 600; margin-bottom: 8px;">Форма расчёта</h1>
            </div>

            <div style="display: flex; gap: 16px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #e0e0e0;">
                <a href="{{ route('managers.calculation') }}" style="padding: 8px 16px; background-color: #0084ff; color: white; border-radius: 6px; text-decoration: none; font-weight: 500; transition: all 0.2s;">
                    Расчёт прибыли
                </a>
                <a href="{{ route('managers.reports') }}" style="padding: 8px 16px; background-color: #f0f0f0; color: #333; border-radius: 6px; text-decoration: none; font-weight: 500; transition: all 0.2s; border: 1px solid #e0e0e0;">
                    Отчёты
                </a>
                <a href="{{ route('managers.history') }}" style="padding: 8px 16px; background-color: #f0f0f0; color: #333; border-radius: 6px; text-decoration: none; font-weight: 500; transition: all 0.2s; border: 1px solid #e0e0e0;">
                    История расчётов
                </a>
            </div>

            <div style="background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 32px;">
                <form id="calculationForm">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Название отчета</label>
                            <input type="text" class="form-control" name="report_name" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="Название">
                        </div>

                         <div class="col-md-4">
                           <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">НДС</label>
                            <select class="form-control" id="nds" name="nds_id" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                                <option value="">Без НДС</option>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" name="nds_percent" id="nds_percent_hidden" value="0">
                    <input type="hidden" name="report_id" id="report_id_hidden" value="">

                    <input type="hidden" name="selling_name" id="selling_name_hidden">
                    <input type="hidden" name="date" id="date_hidden" value="{{ date('Y-m-d') }}">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Покупаю</label>
                            <input type="text" class="form-control" name="buying_name" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="Название товара">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Продаю</label>
                            <select class="form-control" id="selling_type" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                                <option value="inn">ИП ПВВ</option>
                                <option value="fvn">ИП ФВН</option>
                                <option value="ooo">СЗН</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">СПК</label>
                            <select class="form-control" name="spk" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;">
                                <option value="N">НЕТ</option>
                                <option value="Y">ДА</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Цена покупки, р.</label>
                            <input type="number" class="form-control" name="purchase_price" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="0.00" step="0.01" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Кол-во изделий, шт.</label>
                            <input type="number" class="form-control" name="quantity" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="0" step="1" value="">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Сумма покупки, руб.</label>
                            <input type="number" class="form-control" name="purchase_sum" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px; background-color: #f9f9f9;" placeholder="0.00" readonly>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Наценка, %</label>
                            <input type="number" class="form-control" name="markup_percent" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="0.00" step="0.1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Цена продажи, руб.</label>
                            <input type="number" class="form-control" name="selling_price" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px; background-color: #f9f9f9;" placeholder="0.00" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Сумма продажи, руб.</label>
                            <input type="number" class="form-control" name="selling_sum" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px; background-color: #f9f9f9;" placeholder="0.00" readonly>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">PRF, %</label>
                            <input type="number" class="form-control" name="prf_percent" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px; background-color: #f9f9f9;" placeholder="0.00" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Выплата по сделке, руб.</label>
                            <input type="number" class="form-control" name="deal_payment" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px; background-color: #f9f9f9;" placeholder="0.00" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Выплата с 1 шт., руб.</label>
                            <input type="number" class="form-control" name="per_unit_payment" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px; background-color: #f9f9f9;" placeholder="0.00" readonly>
                        </div>
                    </div>

                    <div class="row mb-4" style="border-top: 2px solid #e0e0e0; padding-top: 16px;">
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Сумма в руки, руб</label>
                            <input type="number" class="form-control" name="in_the_hand" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px;" placeholder="0.00" step="0.1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Сумма в счет, руб.</label>
                            <input type="number" class="form-control" name="in_the_deal" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px; background-color: #f9f9f9;" placeholder="0.00" readonly>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Сумма в руки + Цена продажи, руб</label>
                            <input type="number" class="form-control" name="in_the_hand_sum" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px; background-color: #f9f9f9;" placeholder="0.00" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500; margin-bottom: 8px; display: block;">Сумма в счет + Сумма продажи, руб.</label>
                            <input type="number" class="form-control" name="in_the_deal_sum" style="border-radius: 6px; border: 1px solid #e0e0e0; padding: 10px 12px; background-color: #f9f9f9;" placeholder="0.00" readonly>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e0e0e0;">
                        <button type="button" id="calculateBtn" class="btn" style="flex: 1; background-color: #e8d5f2; color: #6c3fa0; border: none; border-radius: 6px; padding: 10px 16px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                            Рассчитать
                        </button>
                        <button type="button" id="saveReportBtn" class="btn" style="flex: 1; background-color: #d5e8f2; color: #3f6ca0; border: none; border-radius: 6px; padding: 10px 16px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                            Сохранить в отчёт
                        </button>
                        <button type="button" id="saveHistoryBtn" class="btn" style="flex: 1; background-color: #e8f2d5; color: #6ca03f; border: none; border-radius: 6px; padding: 10px 16px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                            Сохранить в историю
                        </button>
                        <button type="button" id="clearBtn" class="btn" style="flex: 1; background-color: #f2d5d5; color: #a03f3f; border: none; border-radius: 6px; padding: 10px 16px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                            Очистить всё
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="notificationContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

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

    a:hover {
        opacity: 0.9;
    }

    .notification {
        padding: 14px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        margin-bottom: 12px;
        min-width: 300px;
        animation: slideIn 0.3s ease-out;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }

    .notification.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .notification.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .notification.info {
        background-color: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .notification-icon {
        font-size: 18px;
        flex-shrink: 0;
    }

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .notification.hide {
        animation: slideOut 0.3s ease-out forwards;
    }
</style>

<script>
    function showNotification(message, type = 'success') {
        const container = document.getElementById('notificationContainer');
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;

        const icons = {
            success: '✓',
            error: '✕',
            info: 'ℹ'
        };

        notification.innerHTML = `
            <span class="notification-icon">${icons[type]}</span>
            <span>${message}</span>
        `;

        container.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('hide');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    function loadNdsForType(counteragentType, selectedNdsId = null) {
        const ndsContainer = document.getElementById('nds').closest('.col-md-4');
        if (counteragentType === 'inn') {
            ndsContainer.style.display = 'none';
            document.getElementById('nds_percent_hidden').value = '0';
        } else {
            // fvn и ooo показывают НДС
            ndsContainer.style.display = 'block';
            fetch(`{{ route('managers.get-nds') }}`)
                .then(response => response.json())
                .then(data => {
                    const ndsSelect = document.getElementById('nds');
                    ndsSelect.innerHTML = '<option value="">Без НДС</option>';
                    data.forEach(nds => {
                        if (nds.code_name == "nds_standart") return;
                        const option = document.createElement('option');
                        option.value = nds.id;
                        option.textContent = `${nds.title}`;
                        option.dataset.percent = nds.percent;
                        ndsSelect.appendChild(option);
                    });
                    
                    if (selectedNdsId) {
                        ndsSelect.value = selectedNdsId;
                        const selectedOption = ndsSelect.options[ndsSelect.selectedIndex];
                        const ndsPercent = selectedOption.dataset.percent || '0';
                        document.getElementById('nds_percent_hidden').value = ndsPercent;
                    }
                })
                .catch(error => console.error('Ошибка при загрузке НДС:', error));
        }
    }

    function initializeForm() {
        const sellingTypeSelect = document.getElementById('selling_type');
        const counteragentType = sellingTypeSelect.value;
        
        const sellingNames = {
            'inn': 'ИП (ИНН)',
            'fvn': 'ИП (ФВН)',
            'ooo': 'ООО (УСН)'
        };
        document.getElementById('selling_name_hidden').value = sellingNames[counteragentType] || '';

        loadNdsForType(counteragentType);

        fetch(`{{ route('managers.get-variables') }}?counteragent_type=${counteragentType}`)
            .then(response => response.json())
            .then(data => {
                console.log('ok');
            })
            .catch(error => {
                console.error('Ошибка при загрузке переменных:', error);
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const loadedData = sessionStorage.getItem('loadReportData');
        if (loadedData) {
            const data = JSON.parse(loadedData);
            const calc = data.calculation;
            const report = data.report;

            console.log('Загруженные данные:', data);

            document.getElementsByName('report_name')[0].value = report.report_title;
            document.getElementById('report_id_hidden').value = report.id;
            document.getElementsByName('buying_name')[0].value = calc.buying_name;
            
            const sellingType = calc.selling_name.includes('ИП (ИНН)') ? 'inn' : (calc.selling_name.includes('ИП (ФВН)') ? 'fvn' : 'ooo');
            document.getElementById('selling_type').value = sellingType;

            document.getElementsByName('spk')[0].value = calc.spk;
            document.getElementsByName('purchase_price')[0].value = calc.purchase_price;
            document.getElementsByName('quantity')[0].value = calc.quantity;
            document.getElementsByName('markup_percent')[0].value = calc.markup_percent;
            document.getElementsByName('in_the_hand')[0].value = calc.in_the_hand;
            
            document.getElementById('calculationForm').dataset.calculationId = calc.id;

            sessionStorage.removeItem('loadReportData');

            initializeForm();

            setTimeout(() => {
                if (calc.nds_id) {
                    const counteragentType = calc.selling_name.includes('ИП (ИНН)') ? 'inn' : (calc.selling_name.includes('ИП (ФВН)') ? 'fvn' : 'ooo');
                    if (counteragentType === 'ooo' || counteragentType === 'fvn') {
                        loadNdsForType(counteragentType, calc.nds_id);
                    }
                }
                
                const event = new Event('input', { bubbles: true });
                document.getElementsByName('purchase_price')[0].dispatchEvent(event);
            }, 500);
        } else {
            initializeForm();
        }
    });

    function recalculate() {
        const purchasePrice = parseFloat(document.getElementsByName('purchase_price')[0].value) || 0;
        const quantity = parseInt(document.getElementsByName('quantity')[0].value) || 0;
        const markupPercent = parseFloat(document.getElementsByName('markup_percent')[0].value) || 0;

        const purchaseSum = purchasePrice * quantity;
        document.getElementsByName('purchase_sum')[0].value = purchaseSum.toFixed(0);

        const sellingPrice = purchasePrice * (1 + markupPercent / 100);
        document.getElementsByName('selling_price')[0].value = sellingPrice.toFixed(0);

        const sellingSum = sellingPrice * quantity;
        document.getElementsByName('selling_sum')[0].value = sellingSum.toFixed(0);
    }

    document.getElementsByName('purchase_price')[0].addEventListener('input', recalculate);
    document.getElementsByName('quantity')[0].addEventListener('input', recalculate);
    document.getElementsByName('markup_percent')[0].addEventListener('input', recalculate);

    document.getElementById('selling_type').addEventListener('change', function(e) {
        const counteragentType = e.target.value;
        
        if (!counteragentType) {
            console.log('Тип контрагента не выбран');
            document.getElementById('selling_name_hidden').value = '';
            return;
        }

        const sellingNames = {
            'inn': 'ИП (ИНН)',
            'fvn': 'ИП (ФВН)',
            'ooo': 'ООО (УСН)'
        };
        document.getElementById('selling_name_hidden').value = sellingNames[counteragentType] || '';

        loadNdsForType(counteragentType);

        fetch(`{{ route('managers.get-variables') }}?counteragent_type=${counteragentType}`)
            .then(response => response.json())
            .then(data => {
                console.log('ok');
            })
            .catch(error => {
                console.error('Ошибка при загрузке переменных:', error);
            });
    });

    document.getElementById('nds').addEventListener('change', function(e) {
        const selectedOption = e.target.options[e.target.selectedIndex];
        const ndsPercent = selectedOption.dataset.percent || '0';
        document.getElementById('nds_percent_hidden').value = ndsPercent;
    });

    document.getElementById('calculateBtn').addEventListener('click', function(e) {
        e.preventDefault();

        const formData = new FormData(document.getElementById('calculationForm'));
        
        fetch("{{ route('managers.calculate') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementsByName('per_unit_payment')[0].value = data.calculations.perUnitPayment;
                document.getElementsByName('deal_payment')[0].value = data.calculations.managerPayment;
                document.getElementsByName('in_the_deal')[0].value = data.calculations.inTheDeal;
                document.getElementsByName('prf_percent')[0].value = data.calculations.prfPercent;
                document.getElementsByName('in_the_deal_sum')[0].value = data.calculations.sellingSumTotal;
                document.getElementsByName('in_the_hand_sum')[0].value = data.calculations.sellingSumPerUnit;
                showNotification('Расчёт выполнен успешно', 'success');
            } else {
                showNotification('Ошибка при расчёте', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ошибка при отправке данных', 'error');
        });
    });

    document.getElementById('saveReportBtn').addEventListener('click', function(e) {
        e.preventDefault();

        const formData = new FormData(document.getElementById('calculationForm'));
        const calculationId = document.getElementById('calculationForm').dataset.calculationId;
        
        const url = calculationId 
            ? `{{ route('managers.store-drafts-report') }}?calculation_id=${calculationId}`
            : "{{ route('managers.store-drafts-report') }}";
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                document.getElementById('calculationForm').dataset.calculationId = '';
            } else {
                showNotification('Ошибка при сохранении', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ошибка при отправке данных', 'error');
        });
    });

     document.getElementById('saveHistoryBtn').addEventListener('click', function(e) {
        e.preventDefault();

        const formData = new FormData(document.getElementById('calculationForm'));
        const calculationId = document.getElementById('calculationForm').dataset.calculationId;
        const reportId = document.getElementById('report_id_hidden').value;
        
        if (reportId) {
            formData.append('report_id', reportId);
        }
        
        if (calculationId) {
            formData.append('calculation_id', calculationId);
        }
        
        const url = "{{ route('managers.store-report') }}";
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                document.getElementById('calculationForm').dataset.calculationId = '';
            } else {
                showNotification('Ошибка при сохранении', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ошибка при отправке данных', 'error');
        });
    });

    document.getElementById('clearBtn').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('calculationForm').reset();
    });
</script>
@endsection
