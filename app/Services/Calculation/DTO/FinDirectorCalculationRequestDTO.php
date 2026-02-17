<?php

namespace App\Services\Calculation\DTO;

use Illuminate\Http\Request;

class FinDirectorCalculationRequestDTO
{
    public function __construct(
        public readonly float $amount,
        public readonly float $receivedAmount,
        public readonly float $bonusClient,
        public readonly ?string $soldFrom = null,
        public readonly ?int $spkId = null,
        public readonly ?string $spk = null,
        public readonly float $supplierAmount = 0,
        public readonly float $kickback = 0
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            amount: (float)$request->input('amount', 0),
            receivedAmount: (float)$request->input('received_amount', 0),
            bonusClient: (float)$request->input('bonus_client', 0),
            soldFrom: $request->input('sold_from'),
            spkId: $request->input('spk_id') ? (int)$request->input('spk_id') : null,
            spk: $request->input('spk'),
            supplierAmount: (float)$request->input('supplier_amount', 0),
            kickback: (float)$request->input('kickback', 0)
        );
    }
}
