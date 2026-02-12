<?php

namespace App\Services\Calculation\DTO;

use Illuminate\Http\Request;

class FinDirectorCalculationRequestDTO
{
    public function __construct(
        public readonly float $amount,
        public readonly float $receivedAmount,
        public readonly float $bonusClient
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            amount: (float)$request->input('amount', 0),
            receivedAmount: (float)$request->input('received_amount', 0),
            bonusClient: (float)$request->input('bonus_client', 0)
        );
    }
}
