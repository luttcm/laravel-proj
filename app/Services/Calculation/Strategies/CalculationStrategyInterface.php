<?php

namespace App\Services\Calculation\Strategies;

use App\Services\Calculation\DTO\CalculationRequestDTO;

use Illuminate\Support\Collection;

interface CalculationStrategyInterface
{
    public function calculate(CalculationRequestDTO $request, Collection $variables, float $ndsPercentSelling = 0): array;
}
