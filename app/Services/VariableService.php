<?php

namespace App\Services;

use App\Repositories\VariableRepository;
use Exception;

class VariableService
{
    /** @var VariableRepository */
    protected $variableRepository;

    public function __construct(VariableRepository $variableRepository)
    {
        $this->variableRepository = $variableRepository;
    }

    /**
     * @param array<string, mixed> $data
     * @return \App\Models\Variable
     */
    public function createVariable(array $data)
    {
        $value = $this->formatValue($data['value'], $data['type']);
        
        return $this->variableRepository->create([
            'name' => $data['name'],
            'title' => $data['title'],
            'value' => $value,
            'type' => $data['type'],
            'table_type' => $data['table_type'],
            'counteragent_type' => $data['counteragent_type'],
        ]);
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return bool
     */
    public function updateVariable(int $id, array $data)
    {
        $value = $this->formatValue($data['value'], $data['type']);
        
        return $this->variableRepository->update($id, [
            'name' => $data['name'],
            'title' => $data['title'],
            'value' => $value,
            'type' => $data['type'],
            'table_type' => $data['table_type'],
            'counteragent_type' => $data['counteragent_type'],
        ]);
    }

    public function deleteVariable(int $id): bool
    {
        return $this->variableRepository->delete($id);
    }

    protected function formatValue(string $value, string $type): string
    {
        $trimValue = trim($value);

        if (strpos($trimValue, ',') !== false) {
            throw new Exception('Используйте точку в качестве разделителя десятичных чисел');
        }

        if ($type === 'integer') {
            if (!is_numeric($trimValue)) {
                throw new Exception('Целое число: введите число без букв');
            }
            return (string)(int)$trimValue;
        } else {
            if (!is_numeric($trimValue)) {
                throw new Exception('Дробное число: введите число без букв');
            }
            $valueFloat = (float)$trimValue;
            $valueStr = (string)$valueFloat;
            if (strpos($valueStr, '.') === false) {
                $valueStr = $valueStr . '.0';
            }
            return $valueStr;
        }
    }
}
