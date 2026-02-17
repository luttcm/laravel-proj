<?php

namespace App\Services\Calculation\Strategies;

use App\Services\Calculation\DTO\FinDirectorCalculationRequestDTO;
use App\Services\Calculation\DTO\FinDirectorCalculationResultDTO;
use App\Models\Variable;
use Illuminate\Support\Collection;

class FinDirectorCalculationStrategy
{




    public function calculate(FinDirectorCalculationRequestDTO $data): FinDirectorCalculationResultDTO
    {
        $amount = $data->amount;
        $receivedAmount = $data->receivedAmount;
        $bonusClient = $data->bonusClient;
        $supplierAmount = $data->supplierAmount;
        $soldFrom = $data->soldFrom;
        $kickback = $data->kickback;
        $isSpk = ($data->spk === 'Y' || $data->spk === 'УЧАСТВУЕТ') || !empty($data->spkId);

        $remainder = $amount - $receivedAmount;

        // Чистая продажа РУБ. = Сумма счета для КЛИЕНТА - (Надбавка на ОТКАТ + БОНУС КЛИЕНТУ)
        $netSales = $amount - ($kickback + $bonusClient);

        $markup = 0;
        if ($supplierAmount > 0) {
            $markup = ($netSales - $supplierAmount) / $supplierAmount * 100;
        }

        $profit = 0;
        $paymentManager = 0;
        $paymentSpk = 0;

        if ($soldFrom) {
            $companyVars = Variable::where('table_type', 'company')
                ->where('title', $soldFrom)
                ->get()
                ->keyBy('name');

            // Find coefficients with fallback to defaults
            $k_ps_total = (float)($companyVars['k_ps_total']->value ?? Variable::where('name', 'k_ps_total')->value('value') ?? 0.032);
            $k_mgr = (float)($companyVars['k_mgr']->value ?? Variable::where('name', 'k_mgr')->value('value') ?? 0.245);
            $k_spk = (float)($companyVars['k_spk']->value ?? Variable::where('name', 'k_spk')->value('value') ?? 0.2);

            // Calculations based on Net Sales
            $profitAmount = $netSales * $k_ps_total;
            $profit = $profitAmount; // Storing amount as profit per OOO strategy context

            $paymentBase = $netSales * $k_mgr;

            if ($isSpk) {
                $paymentSpk = $paymentBase * $k_spk;
                $paymentManager = $paymentBase - $paymentSpk;
            } else {
                $paymentSpk = 0;
                $paymentManager = $paymentBase;
            }
        }

        return new FinDirectorCalculationResultDTO(
            remainder: $remainder,
            netSales: $netSales,
            paymentManager: $paymentManager,
            paymentSpk: $paymentSpk,
            profit: $profit,
            markup: $markup
        );
    }
}
