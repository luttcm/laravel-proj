<?php

namespace App\Services\Calculation\DTO;

class FinDirectorCalculationResultDTO
{
    public function __construct(
        public readonly float $remainder,
        public readonly float $netSales,
        public readonly float $paymentManager = 0,
        public readonly float $paymentSpk = 0,
        public readonly float $profit = 0,
        public readonly float $markup = 0,
        public readonly float $logisticsBonus = 0,
        public readonly float $finAdminBonus = 0,
        public readonly float $fbrBonus = 0
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'remainder' => $this->remainder,
            'net_sales' => $this->netSales,
            'payment_manager' => $this->paymentManager,
            'payment_spk' => $this->paymentSpk,
            'profit' => $this->profit,
            'markup' => $this->markup,
            'logistics_bonus' => $this->logisticsBonus,
            'fin_admin_bonus' => $this->finAdminBonus,
            'fbr_bonus' => $this->fbrBonus,
        ];
    }
}
