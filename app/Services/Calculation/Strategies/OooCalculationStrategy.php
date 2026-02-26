<?php

namespace App\Services\Calculation\Strategies;

use App\Services\Calculation\DTO\CalculationRequestDTO;
use Illuminate\Support\Collection;

class OooCalculationStrategy implements CalculationStrategyInterface
{
    /**
     * @param CalculationRequestDTO $data
     * @param Collection<string, mixed> $variables
     * @param float $ndsPercentSelling
     * @param float $spkCoefficient
     * @return array<string, mixed>
     */
    public function calculate(CalculationRequestDTO $data, Collection $variables, float $ndsPercentSelling = 18, float $spkCoefficient = 0): array
    {
        $sellingSum = ($data->purchasePrice * (1 + $data->markupPercent / 100)) * $data->quantity;
        $purchaseSum = $data->purchasePrice * $data->quantity;
        $quantity = $data->quantity;
        $spk = $data->spk;
        $inTheHand = $data->inTheHand;
        $ndsPercentPurchase = $data->ndsPercentPurchase;

        $riskReserveRate = (float)($variables['RiskReserveRate']->value ?? 0.05);
        $k_log = (float)($variables['k_log']->value ?? 0.015);
        $k_fin = (float)($variables['k_fin']->value ?? 0.015);
        $k_fbr = (float)($variables['k_fbr']->value ?? 0.002);
        $k_ps_total = (float)($variables['k_ps_total']->value ?? ($k_log + $k_fin + $k_fbr));
        
        $k_mgr = (float)($variables['k_mgr']->value ?? 0.20);
        $rate_ins = (float)($variables['rate_ins']->value ?? 0.30);
        $rate_ndfl = (float)($variables['rate_ndfl']->value ?? 0.13);
        $k_spk = $spkCoefficient;
        
        $counteragentType = strpos($data->sellingType, 'ИП (ФВН)') !== false ? 'fvn' : 'ooo';
        if ($counteragentType === 'ooo') {
            $k_bonus = (float)($variables['k_bonus_ooo']->value ?? 0.20);
        } else {
            $k_bonus = (float)($variables['k_bonus_fvn']->value ?? 0.20);
        }

        $rate_cit = (float)($variables['rate_cit']->value ?? 0.25);

        $inTheDeal = ($inTheHand * $k_bonus) + $inTheHand;
        $inTheDealPerUnit = $quantity > 0 ? $inTheDeal / $quantity : 0;

        if ($ndsPercentPurchase > 0) {
            $ndsIncoming = $purchaseSum / (100 + $ndsPercentPurchase) * $ndsPercentPurchase;
        } else {
            $ndsIncoming = 0;
        }

        $ndsOutgoing = $sellingSum / (100 + $ndsPercentSelling) * $ndsPercentSelling;
        $ndsPaid = $ndsOutgoing - $ndsIncoming;

        $nacenka = $sellingSum - $purchaseSum;
        $P1 = $nacenka - $ndsPaid;
        
        $riskReserve = max(0, $P1 * $riskReserveRate);
        $premBase = max(0, $P1 - $riskReserve);

        $logisticsBonus = $premBase * $k_log;
        $finAdminBonus = $premBase * $k_fin;
        $fbrBonus = $premBase * $k_fbr;
        $premiyaTotal = $premBase * $k_ps_total;

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

        $citBase = max(0, $P1 - $riskReserve - $premiyaTotal - $totalManagerCost - $percentSumm);
        $citTax = $citBase * $rate_cit;

        $totalTaxes = $ndsPaid + $managerNdfl + $socialFunds + $citTax;
        $companyProfit = $P1 - $riskReserve - $premiyaTotal - $managerSalaryBrutto - $socialFunds - $percentSumm - $citTax;
        
        $prfPercent = $sellingSum > 0 ? ($companyProfit / $sellingSum) * 100 : 0;

        return [
            'nacenka' => $nacenka,
            'ndsOutgoing' => $ndsOutgoing,
            'ndsIncoming' => $ndsIncoming,
            'ndsPaid' => $ndsPaid,
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
            'citBase' => $citBase,
            'citTax' => $citTax,
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
