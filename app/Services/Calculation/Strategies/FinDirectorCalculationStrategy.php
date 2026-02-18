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

            $k_ps_total = (float)($companyVars['k_ps_total']->value ?? Variable::where('name', 'k_ps_total')->value('value') ?? 0.032);
            $k_mgr = (float)($companyVars['k_mgr']->value ?? Variable::where('name', 'k_mgr')->value('value') ?? 0.245);
            $k_spk = (float)($companyVars['k_spk']->value ?? Variable::where('name', 'k_spk')->value('value') ?? 0.2);

            $profitAmount = $netSales * $k_ps_total;
            $profit = $profitAmount;

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
