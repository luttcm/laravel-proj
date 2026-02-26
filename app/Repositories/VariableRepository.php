<?php

namespace App\Repositories;

use App\Models\Variable;
use Illuminate\Database\Eloquent\Collection;

class VariableRepository
{
    /**
     * @param string $counteragentType
     * @return Collection<int, Variable>
     */
    public function getCompanyVariablesByType(string $counteragentType): Collection
    {
        return Variable::where('counteragent_type', $counteragentType)
            ->where('table_type', 'company')
            ->get();
    }

    /**
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator<Variable>
     */
    public function getAllCompanyPaginated(int $perPage = 10): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Variable::where('table_type', 'company')->paginate($perPage, ['*'], 'company_page');
    }

    /**
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator<Variable>
     */
    public function getAllFncPaginated(int $perPage = 10): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Variable::where('table_type', 'fnc')->paginate($perPage, ['*'], 'fnc_page');
    }

    public function findById(int $id): ?Variable
    {
        /** @var Variable|null $variable */
        $variable = Variable::find($id);
        return $variable;
    }

    /**
     * @param array<string, mixed> $data
     * @return Variable
     */
    public function create(array $data): Variable
    {
        /** @var Variable $variable */
        $variable = Variable::create($data);
        return $variable;
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $variable = $this->findById($id);
        if (!$variable) {
            return false;
        }
        return $variable->update($data);
    }

    public function delete(int $id): bool
    {
        $variable = $this->findById($id);
        if (!$variable) {
            return false;
        }
        return (bool)$variable->delete();
    }
}
