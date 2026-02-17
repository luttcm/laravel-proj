<?php

use App\Services\Calculation\DTO\FinDirectorCalculationRequestDTO;
use App\Services\Calculation\Strategies\FinDirectorCalculationStrategy;
use App\Models\Variable;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function testRow($label, $data, $expected) {
    echo "Testing $label...\n";
    $strategy = new FinDirectorCalculationStrategy();
    $result = $strategy->calculate($data);
    
    $passed = true;
    foreach ($expected as $key => $val) {
        $actual = round($result->$key, 2);
        if (abs($actual - $val) > 0.01) {
            echo "  [FAIL] $key: expected $val, got $actual\n";
            $passed = false;
        } else {
            echo "  [OK] $key: $actual\n";
        }
    }
    echo $passed ? "  => PASSED\n\n" : "  => FAILED\n\n";
    return $passed;
}

// 0. Setup Mock Data in Variables table
DB::beginTransaction();
try {
    Variable::updateOrCreate(
        ['title' => 'ИП ФВН', 'name' => 'k_ps_total', 'table_type' => 'company'],
        ['value' => '0.001', 'type' => 'text', 'counteragent_type' => 'ooo']
    );
    Variable::updateOrCreate(
        ['title' => 'ИП ФВН', 'name' => 'k_mgr', 'table_type' => 'company'],
        ['value' => '0.001', 'type' => 'text', 'counteragent_type' => 'ooo']
    );
    Variable::updateOrCreate(
        ['title' => 'ЮСИНТА', 'name' => 'k_ps_total', 'table_type' => 'company'],
        ['value' => '0.4262', 'type' => 'text', 'counteragent_type' => 'ooo']
    );
    Variable::updateOrCreate(
        ['title' => 'ЮСИНТА', 'name' => 'k_mgr', 'table_type' => 'company'],
        ['value' => '0.2', 'type' => 'text', 'counteragent_type' => 'ooo']
    );
    Variable::updateOrCreate(
        ['name' => 'k_spk'],
        ['value' => '0.2', 'type' => 'text', 'counteragent_type' => 'ooo', 'title' => 'k_spk', 'table_type' => 'company']
    );

    // Row 1 Test
    $row1 = new FinDirectorCalculationRequestDTO(
        amount: 261558.00,
        receivedAmount: 285558.00,
        bonusClient: 24000.00,
        soldFrom: 'ИП ФВН',
        spk: 'не участвует',
        supplierAmount: 211144.37,
        kickback: 10000.00
    );
    // netSales = 261558 - (10000 + 24000) = 227558.00
    // profit = 227558 * 0.001 = 227.56
    // paymentManager = 227558 * 0.001 = 227.56
    testRow("Excel Row 1 (ИП ФВН)", $row1, [
        'remainder' => -24000.00,
        'netSales' => 227558.00,
        'markup' => 7.77,
        'profit' => 227.56,
        'paymentManager' => 227.56,
        'paymentSpk' => 0.00
    ]);

    // Row 2 Test
    $row2 = new FinDirectorCalculationRequestDTO(
        amount: 20183910.00,
        receivedAmount: 20183910.00,
        bonusClient: 0.00,
        soldFrom: 'ЮСИНТА',
        spk: 'не участвует',
        supplierAmount: 10369350.56,
        kickback: 0.00
    );
    // netSales = 20183910 - 0 = 20183910
    // profit = 20183910 * 0.4262 = 8602382.44
    // paymentManager = 20183910 * 0.2 = 4036782.00
    testRow("Excel Row 2 (ЮСИНТА)", $row2, [
        'remainder' => 0.00,
        'netSales' => 20183910.00,
        'markup' => 94.65,
        'profit' => 8602382.44,
        'paymentManager' => 4036782.00,
        'paymentSpk' => 0.00
    ]);

} finally {
    DB::rollBack();
}
