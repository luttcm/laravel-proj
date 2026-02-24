<?php

namespace App\Repositories;

use App\Models\Variable;
use Illuminate\Database\Eloquent\Collection;

class VariableRepository
{
    public function getCompanyVariablesByType(string $counteragentType): Collection
    {
        return Variable::where('counteragent_type', $counteragentType)
            ->where('table_type', 'company')
            ->get();
    }

    public function getAllCompanyPaginated(int $perPage = 10)
    {
        return Variable::where('table_type', 'company')->paginate($perPage, ['*'], 'company_page');
    }

    public function getAllFncPaginated(int $perPage = 10)
    {
        return Variable::where('table_type', 'fnc')->paginate($perPage, ['*'], 'fnc_page');
    }

    public function findById(int $id): ?Variable
    {
        return Variable::findOrFail($id);
    }

    public function create(array $data): Variable
    {
        return Variable::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $variable = $this->findById($id);
        return $variable->update($data);
    }

    public function delete(int $id): bool
    {
        $variable = $this->findById($id);
        return $variable->delete();
    }
}
