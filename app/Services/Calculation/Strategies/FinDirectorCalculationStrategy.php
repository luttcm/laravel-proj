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
        $logisticsBonus = 0;
        $finAdminBonus = 0;
        $fbrBonus = 0;

        if ($soldFrom) {
            $companyVars = Variable::where('table_type', 'company')
                ->where('title', $soldFrom)
                ->get()
                ->keyBy('name');

            $getVar = function($name, $default) use ($companyVars) {
                return (float)($companyVars[$name]->value ?? Variable::where('name', $name)->value('value') ?? $default);
            };

            $k_ps_total = $getVar('k_ps_total', 0.032);
            $k_mgr = $getVar('k_mgr', 0.245);
            $k_spk = $getVar('k_spk', 0.2);
            $k_log = $getVar('k_log', 0.015);
            $k_fin = $getVar('k_fin', 0.015);
            $k_fbr = $getVar('k_fbr', 0.002);
            $rate_ausn = $getVar('rate_ausn', 0.08);
            $riskReserveRate = $getVar('RiskReserveRate', 0.05);
            $rate_ndfl = $getVar('rate_ndfl', 0.13);

            $nacenka = $netSales - $supplierAmount;
            $ausn = $netSales * $rate_ausn;
            $P1 = $nacenka - $ausn;
            $riskReserve = max(0, $P1 * $riskReserveRate);
            $premBase = max(0, $P1 - $riskReserve);

            $logisticsBonus = ($premBase * $k_log) * (1 - $rate_ndfl);
            $finAdminBonus = ($premBase * $k_fin) * (1 - $rate_ndfl);
            $fbrBonus = ($premBase * $k_fbr) * (1 - $rate_ndfl);

            $logisticsBonusGross = $premBase * $k_log;
            $finAdminBonusGross = $premBase * $k_fin;
            $fbrBonusGross = $premBase * $k_fbr;
            $premiyaTotalGross = $logisticsBonusGross + $finAdminBonusGross + $fbrBonusGross;

            $managerBase = max(0, $premBase - $premiyaTotalGross);
            $managerSalaryBrutto = $managerBase * $k_mgr;
            $totalManagerPayment = $managerSalaryBrutto * (1 - $rate_ndfl);

            if ($isSpk) {
                $paymentSpk = $totalManagerPayment * $k_spk;
                $paymentManager = $totalManagerPayment - $paymentSpk;
            } else {
                $paymentSpk = 0;
                $paymentManager = $totalManagerPayment;
            }

            $profit = $netSales * $k_ps_total;
        }

        return new FinDirectorCalculationResultDTO(
            remainder: $remainder,
            netSales: $netSales,
            paymentManager: $paymentManager,
            paymentSpk: $paymentSpk,
            profit: $profit,
            markup: $markup,
            logisticsBonus: $logisticsBonus,
            finAdminBonus: $finAdminBonus,
            fbrBonus: $fbrBonus
        );
    }
}
