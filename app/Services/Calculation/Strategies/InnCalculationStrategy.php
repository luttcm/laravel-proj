<?php

namespace App\Services\Calculation\Strategies;

use App\Services\Calculation\DTO\CalculationRequestDTO;
use Illuminate\Support\Collection;

class InnCalculationStrategy implements CalculationStrategyInterface
{
    /**
     * @param CalculationRequestDTO $data
     * @param Collection<string, mixed> $variables
     * @param float $ndsPercentSelling
     * @param float $spkCoefficient
     * @return array<string, mixed>
     */
    public function calculate(CalculationRequestDTO $data, Collection $variables, float $ndsPercentSelling = 0, float $spkCoefficient = 0): array
    {
        $sellingSum = ($data->purchasePrice * (1 + $data->markupPercent / 100)) * $data->quantity;
        $purchaseSum = $data->purchasePrice * $data->quantity;
        $quantity = $data->quantity;
        $spk = $data->spk;
        $inTheHand = $data->inTheHand;

        $riskReserveRate = (float)($variables['RiskReserveRate']->value ?? 0.05);
        $k_log = (float)($variables['k_log']->value ?? 0.015);
        $k_fin = (float)($variables['k_fin']->value ?? 0.015);
        $k_fbr = (float)($variables['k_fbr']->value ?? 0.002);
        
        $k_mgr = (float)($variables['k_mgr']->value ?? 0.245);
        $rate_ins = (float)($variables['rate_ins']->value ?? 0.01);
        $rate_ndfl = (float)($variables['rate_ndfl']->value ?? 0.13);
        $k_spk = $spkCoefficient;
        $k_bonus = (float)($variables['k_bonus_inn']->value ?? 0.20);
        $rate_ausn = (float)($variables['rate_ausn']->value ?? 0.08);

        $inTheDeal = ($inTheHand * $k_bonus) + $inTheHand;
        $inTheDealPerUnit = $quantity > 0 ? $inTheDeal / $quantity : 0;

        $nacenka = $sellingSum - $purchaseSum;
        $ausn = $sellingSum * $rate_ausn;
        $P1 = $nacenka - $ausn;
        
        $riskReserve = max(0, $P1 * $riskReserveRate);
        $premBase = max(0, $P1 - $riskReserve);

        $logisticsBonus = $premBase * $k_log;
        $finAdminBonus = $premBase * $k_fin;
        $fbrBonus = $premBase * $k_fbr;
        $premiyaTotal = $logisticsBonus + $finAdminBonus + $fbrBonus;

        $managerBase = max(0, $premBase - $premiyaTotal);
        $managerSalaryBrutto = $managerBase * $k_mgr;
        $managerNdfl = $managerSalaryBrutto * $rate_ndfl;

        $socialFunds = $managerSalaryBrutto * $rate_ins;
        $percentSumm = $premiyaTotal * $rate_ins;

        $managerPayment = $managerSalaryBrutto - $managerNdfl;       
        $totalManagerCost = $managerSalaryBrutto + $socialFunds;
        $perUnitPayment = $quantity > 0 ? $managerPayment / $quantity : 0;

        $spkPayment = 0;
        if ($spk == 'Y' || $data->spkId) {
            $spkPayment = $managerPayment * $k_spk;
            $managerPayment -= $spkPayment;
            $perUnitPayment = $quantity > 0 ? $managerPayment / $quantity : 0;
        }

        $totalTaxes = $ausn + $managerNdfl + $socialFunds;
        $companyProfit = $P1 - $riskReserve - $premiyaTotal - $managerSalaryBrutto - $socialFunds - $percentSumm;
        $prfPercent = $sellingSum > 0 ? ($companyProfit / $sellingSum) * 100 : 0;

        return [
            'nacenka' => $nacenka,
            'ausn' => $ausn,
            'P1' => $P1,
            'riskReserve' => $riskReserve,
            'premBase' => $premBase,
            'logisticsBonus' => $logisticsBonus,
            'finAdminBonus' => $finAdminBonus,
            'fbrBonus' => $fbrBonus,
            'premiyaTotal' => $premiyaTotal,
            'managerBase' => $managerBase,
            'managerSalaryBrutto' => $managerSalaryBrutto,
            'managerNdfl' => $managerNdfl,
            'socialFunds' => $socialFunds,
            'totalManagerCost' => $totalManagerCost,
            'managerPayment' => $managerPayment,
            'spkPayment' => $spkPayment,
            'perUnitPayment' => $perUnitPayment,
            'totalTaxes' => $totalTaxes,
            'companyProfit' => $companyProfit,
            'prfPercent' => $prfPercent,
            'spk' => $spk,
            'inTheDeal' => $inTheDeal,
            'sellingSumPerUnit' => $quantity > 0 ? ($sellingSum / $quantity + $inTheDealPerUnit) : 0,
            'sellingSumTotal' => $sellingSum + $inTheDealPerUnit * $quantity
        ];
    }
}
