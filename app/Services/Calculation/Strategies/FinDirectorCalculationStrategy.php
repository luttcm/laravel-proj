<?php

namespace App\Services\Calculation\Strategies;

use App\Services\Calculation\DTO\FinDirectorCalculationRequestDTO;
use App\Services\Calculation\DTO\FinDirectorCalculationResultDTO;

class FinDirectorCalculationStrategy
{
    public function calculate(FinDirectorCalculationRequestDTO $request): FinDirectorCalculationResultDTO
    {
        // остаток руб = Сумма счета для КЛИЕНТА - Поступило реально, руб.
        $remainder = $request->amount - $request->receivedAmount;

        // Чистая продажа РУБ. = Сумма счета для КЛИЕНТА - ( Поступило реально, руб. - БОНУС КЛИЕНТУ)
        $netSales = $request->amount - ($request->receivedAmount - $request->bonusClient);

        return new FinDirectorCalculationResultDTO(
            remainder: $remainder,
            netSales: $netSales
        );
    }
}
