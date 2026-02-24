<?php

namespace App\Services;

use App\Repositories\NdsRepository;
use Exception;

class NdsService
{
    protected $ndsRepository;

    public function __construct(NdsRepository $ndsRepository)
    {
        $this->ndsRepository = $ndsRepository;
    }

    public function createNds(array $data)
    {
        $value = $this->formatPercent($data['percent']);
        
        return $this->ndsRepository->create([
            'code_name' => $data['code_name'],
            'title' => $data['title'],
            'percent' => $value,
        ]);
    }

    public function updateNds(int $id, array $data)
    {
        $value = $this->formatPercent($data['percent']);
        
        return $this->ndsRepository->update($id, [
            'code_name' => $data['code_name'],
            'title' => $data['title'],
            'percent' => $value,
        ]);
    }

    public function deleteNds(int $id)
    {
        return $this->ndsRepository->delete($id);
    }

    protected function formatPercent(string $percent): string
    {
        $value = trim($percent);

        if (strpos($value, ',') !== false) {
            throw new Exception('Используйте точку в качестве разделителя десятичных чисел');
        }
        if (!is_numeric($value)) {
            throw new Exception('Дробное число: введите число без букв');
        }
        
        $valueFloat = (float)$value;
        $valueStr = (string)$valueFloat;
        
        if (strpos($valueStr, '.') === false) {
            $valueStr = $valueStr . '.0';
        }
        
        return $valueStr;
    }
}
