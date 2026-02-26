<?php

namespace App\Services\Calculation\DTO;

class CalculationRequestDTO
{
    public function __construct(
        public readonly string $sellingType,
        public readonly string $spk,
        public readonly float $inTheHand,
        public readonly float $purchasePrice,
        public readonly int $quantity,
        public readonly float $markupPercent,
        public readonly float $ndsPercentPurchase = 0,
        public readonly ?int $spkId = null
    ) {}

    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return new self(
            sellingType: (string)$request->input('selling_name'),
            spk: (string)$request->input('spk'),
            inTheHand: (float)$request->input('in_the_hand', 0),
            purchasePrice: (float)$request->input('purchase_price', 0),
            quantity: (int)$request->input('quantity', 0) ?: 1,
            markupPercent: (float)$request->input('markup_percent', 0),
            ndsPercentPurchase: (float)$request->input('nds_percent', 0),
            spkId: $request->input('spk_id') ? (int)$request->input('spk_id') : null
        );
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            sellingType: (string)($data['selling_name'] ?? ''),
            spk: (string)($data['spk'] ?? ''),
            inTheHand: (float)($data['in_the_hand'] ?? 0),
            purchasePrice: (float)($data['purchase_price'] ?? 0),
            quantity: (int)($data['quantity'] ?? 1) ?: 1,
            markupPercent: (float)($data['markup_percent'] ?? 0),
            ndsPercentPurchase: (float)($data['nds_percent'] ?? 0),
            spkId: isset($data['spk_id']) ? (int)$data['spk_id'] : null
        );
    }
}
