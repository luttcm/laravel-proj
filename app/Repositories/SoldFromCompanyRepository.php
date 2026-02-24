<?php

namespace App\Repositories;

use App\Models\SoldFromCompany;
use Illuminate\Database\Eloquent\Collection;

class SoldFromCompanyRepository
{
    public function getAllPaginated(int $perPage = 10)
    {
        return SoldFromCompany::paginate($perPage);
    }

    public function findById(int $id): ?SoldFromCompany
    {
        return SoldFromCompany::findOrFail($id);
    }

    public function create(array $data): SoldFromCompany
    {
        return SoldFromCompany::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $company = $this->findById($id);
        return $company->update($data);
    }

    public function delete(int $id): bool
    {
        $company = $this->findById($id);
        return $company->delete();
    }
}
