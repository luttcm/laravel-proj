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
        return self::fromArray($request->all());
    }

    public static function fromArray(array $data): self
    {
        return new self(
            amount: (float)($data['amount'] ?? 0),
            receivedAmount: (float)($data['received_amount'] ?? 0),
            bonusClient: (float)($data['bonus_client'] ?? 0),
            soldFrom: $data['sold_from'] ?? null,
            spkId: isset($data['spk_id']) ? (int)$data['spk_id'] : null,
            spk: $data['spk'] ?? null,
            supplierAmount: (float)($data['supplier_amount'] ?? 0),
            kickback: (float)($data['kickback'] ?? 0)
        );
    }
}
