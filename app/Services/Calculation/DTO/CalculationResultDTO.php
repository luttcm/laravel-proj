<?php

namespace App\Services\Calculation\DTO;

class CalculationResultDTO
{
    public function __construct(
        public readonly float $nacenka,
        public readonly float $P1,
        public readonly float $riskReserve,
        public readonly float $premBase,
        public readonly float $logisticsBonus,
        public readonly float $finAdminBonus,
        public readonly float $fbrBonus,
        public readonly float $premiyaTotal,
        public readonly float $managerBase,
        public readonly float $managerSalaryBrutto,
        public readonly float $managerNdfl,
        public readonly float $socialFunds,
        public readonly float $totalManagerCost,
        public readonly float $managerPayment,
        public readonly float $spkPayment,
        public readonly float $perUnitPayment,
        public readonly float $totalTaxes,
        public readonly float $companyProfit,
        public readonly float $prfPercent,
        public readonly string $spk,
        public readonly float $inTheDeal,
        public readonly float $sellingSumPerUnit,
        public readonly float $sellingSumTotal,

        public readonly ?float $ausn = null,
        public readonly ?float $ndsOutgoing = null,
        public readonly ?float $ndsIncoming = null,
        public readonly ?float $ndsPaid = null,
        public readonly ?float $citBase = null,
        public readonly ?float $citTax = null
    ) {}

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            nacenka: (float)($data['nacenka'] ?? 0),
            P1: (float)($data['P1'] ?? 0),
            riskReserve: (float)($data['riskReserve'] ?? 0),
            premBase: (float)($data['premBase'] ?? 0),
            logisticsBonus: (float)($data['logisticsBonus'] ?? 0),
            finAdminBonus: (float)($data['finAdminBonus'] ?? 0),
            fbrBonus: (float)($data['fbrBonus'] ?? 0),
            premiyaTotal: (float)($data['premiyaTotal'] ?? 0),
            managerBase: (float)($data['managerBase'] ?? 0),
            managerSalaryBrutto: (float)($data['managerSalaryBrutto'] ?? 0),
            managerNdfl: (float)($data['managerNdfl'] ?? 0),
            socialFunds: (float)($data['socialFunds'] ?? 0),
            totalManagerCost: (float)($data['totalManagerCost'] ?? 0),
            managerPayment: (float)($data['managerPayment'] ?? 0),
            spkPayment: (float)($data['spkPayment'] ?? 0),
            perUnitPayment: (float)($data['perUnitPayment'] ?? 0),
            totalTaxes: (float)($data['totalTaxes'] ?? 0),
            companyProfit: (float)($data['companyProfit'] ?? 0),
            prfPercent: (float)($data['prfPercent'] ?? 0),
            spk: (string)($data['spk'] ?? 'N'),
            inTheDeal: (float)($data['inTheDeal'] ?? 0),
            sellingSumPerUnit: (float)($data['sellingSumPerUnit'] ?? 0),
            sellingSumTotal: (float)($data['sellingSumTotal'] ?? 0),
            ausn: isset($data['ausn']) ? (float)$data['ausn'] : null,
            ndsOutgoing: isset($data['ndsOutgoing']) ? (float)$data['ndsOutgoing'] : null,
            ndsIncoming: isset($data['ndsIncoming']) ? (float)$data['ndsIncoming'] : null,
            ndsPaid: isset($data['ndsPaid']) ? (float)$data['ndsPaid'] : null,
            citBase: isset($data['citBase']) ? (float)$data['citBase'] : null,
            citTax: isset($data['citTax']) ? (float)$data['citTax'] : null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'nacenka' => $this->nacenka,
            'P1' => $this->P1,
            'riskReserve' => $this->riskReserve,
            'premBase' => $this->premBase,
            'logisticsBonus' => $this->logisticsBonus,
            'finAdminBonus' => $this->finAdminBonus,
            'fbrBonus' => $this->fbrBonus,
            'premiyaTotal' => $this->premiyaTotal,
            'managerBase' => $this->managerBase,
            'managerSalaryBrutto' => $this->managerSalaryBrutto,
            'managerNdfl' => $this->managerNdfl,
            'socialFunds' => $this->socialFunds,
            'totalManagerCost' => $this->totalManagerCost,
            'managerPayment' => $this->managerPayment,
            'spkPayment' => $this->spkPayment,
            'perUnitPayment' => $this->perUnitPayment,
            'totalTaxes' => $this->totalTaxes,
            'companyProfit' => $this->companyProfit,
            'prfPercent' => $this->prfPercent,
            'spk' => $this->spk,
            'inTheDeal' => $this->inTheDeal,
            'sellingSumPerUnit' => $this->sellingSumPerUnit,
            'sellingSumTotal' => $this->sellingSumTotal,
        ];

        if ($this->ausn !== null) $data['ausn'] = $this->ausn;
        if ($this->ndsOutgoing !== null) $data['ndsOutgoing'] = $this->ndsOutgoing;
        if ($this->ndsIncoming !== null) $data['ndsIncoming'] = $this->ndsIncoming;
        if ($this->ndsPaid !== null) $data['ndsPaid'] = $this->ndsPaid;
        if ($this->citBase !== null) $data['citBase'] = $this->citBase;
        if ($this->citTax !== null) $data['citTax'] = $this->citTax;

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    public function formatForResponse(): array
    {
        $calculations = [
            'nacenka' => round($this->nacenka, 0, PHP_ROUND_HALF_UP),
            'P1' => round($this->P1, 0, PHP_ROUND_HALF_UP),
            'riskReserve' => round($this->riskReserve, 0, PHP_ROUND_HALF_UP),
            'premBase' => round($this->premBase, 0, PHP_ROUND_HALF_UP),
            'logisticsBonus' => round($this->logisticsBonus, 0, PHP_ROUND_HALF_UP),
            'finAdminBonus' => round($this->finAdminBonus, 0, PHP_ROUND_HALF_UP),
            'fbrBonus' => round($this->fbrBonus, 0, PHP_ROUND_HALF_UP),
            'premiyaTotal' => round($this->premiyaTotal, 0, PHP_ROUND_HALF_UP),
            'managerBase' => round($this->managerBase, 0, PHP_ROUND_HALF_UP),
            'managerSalaryBrutto' => round($this->managerSalaryBrutto, 0, PHP_ROUND_HALF_UP),
            'managerNdfl' => round($this->managerNdfl, 0, PHP_ROUND_HALF_UP),
            'socialFunds' => round($this->socialFunds, 0, PHP_ROUND_HALF_UP),
            'totalManagerCost' => round($this->totalManagerCost, 0, PHP_ROUND_HALF_UP),
            'managerPayment' => round($this->managerPayment, 0, PHP_ROUND_HALF_UP),
            'spkPayment' => round($this->spkPayment, 0, PHP_ROUND_HALF_UP),
            'perUnitPayment' => round($this->perUnitPayment, 0, PHP_ROUND_HALF_UP),
            'totalTaxes' => round($this->totalTaxes, 0, PHP_ROUND_HALF_UP),
            'companyProfit' => round($this->companyProfit, 0, PHP_ROUND_HALF_UP),
            'prfPercent' => round($this->prfPercent, 2, PHP_ROUND_HALF_UP),
            'spk' => $this->spk,
            'inTheDeal' => round($this->inTheDeal, 0, PHP_ROUND_HALF_UP),
            'sellingSumPerUnit' => round($this->sellingSumPerUnit, 0, PHP_ROUND_HALF_UP),
            'sellingSumTotal' => round($this->sellingSumTotal, 0, PHP_ROUND_HALF_UP),
        ];

        if ($this->ausn !== null) {
            $calculations['ausn'] = round($this->ausn, 0, PHP_ROUND_HALF_UP);
        }

        if ($this->ndsOutgoing !== null) {
            $calculations['ndsOutgoing'] = round($this->ndsOutgoing, 0, PHP_ROUND_HALF_UP);
        }
        if ($this->ndsIncoming !== null) {
            $calculations['ndsIncoming'] = round($this->ndsIncoming, 0, PHP_ROUND_HALF_UP);
        }
        if ($this->ndsPaid !== null) {
            $calculations['ndsPaid'] = round($this->ndsPaid, 0, PHP_ROUND_HALF_UP);
        }
        if ($this->citBase !== null) {
            $calculations['citBase'] = round($this->citBase, 0, PHP_ROUND_HALF_UP);
        }
        if ($this->citTax !== null) {
            $calculations['citTax'] = round($this->citTax, 0, PHP_ROUND_HALF_UP);
        }

        return $calculations;
    }
}
