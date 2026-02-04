<?php

namespace App\Services\Calculation;

use App\Models\Variable;
use App\Models\Nds;
use App\Services\Calculation\DTO\CalculationRequestDTO;
use App\Services\Calculation\Strategies\InnCalculationStrategy;
use App\Services\Calculation\Strategies\OooCalculationStrategy;

use App\Services\Calculation\DTO\CalculationResultDTO;

class CalculationService
{
    public function calculate(CalculationRequestDTO $data): CalculationResultDTO
    {
        $sellingType = $data->sellingType;
        $counteragentType = strpos($sellingType, 'ИП (ИНН)') !== false ? 'inn' : (strpos($sellingType, 'ИП (ФВН)') !== false ? 'fvn' : 'ooo');
        $dbCounteragentType = ($counteragentType === 'fvn') ? 'ooo' : $counteragentType;

        $variables = Variable::where('counteragent_type', $dbCounteragentType)
            ->where('table_type', 'company')
            ->get()
            ->keyBy('name');

        if ($counteragentType === 'inn') {
            $strategy = new InnCalculationStrategy();
            $result = $strategy->calculate($data, $variables);
        } else {
            $standardNds = Nds::where('code_name', 'nds_standart')->first();
            $ndsPercentSelling = $standardNds ? (float)$standardNds->percent : 22;
            
            $strategy = new OooCalculationStrategy();
            $result = $strategy->calculate($data, $variables, $ndsPercentSelling);
        }

        return CalculationResultDTO::fromArray($result);
    }
}
