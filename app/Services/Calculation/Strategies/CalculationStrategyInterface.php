<?php

namespace App\Services\Calculation\Strategies;

use App\Services\Calculation\DTO\CalculationRequestDTO;

use Illuminate\Support\Collection;

interface CalculationStrategyInterface
{
    /**
     * @param CalculationRequestDTO $request
     * @param Collection<string, mixed> $variables
     * @param float $ndsPercentSelling
     * @param float $spkCoefficient
     * @return array<string, mixed>
     */
    public function calculate(CalculationRequestDTO $request, Collection $variables, float $ndsPercentSelling = 0, float $spkCoefficient = 0): array;
}
