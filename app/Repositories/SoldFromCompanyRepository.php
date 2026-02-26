<?php

namespace App\Repositories;

use App\Models\SoldFromCompany;
use Illuminate\Database\Eloquent\Collection;

class SoldFromCompanyRepository
{
    /**
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator<SoldFromCompany>
     */
    public function getAllPaginated(int $perPage = 10): \Illuminate\Pagination\LengthAwarePaginator
    {
        return SoldFromCompany::paginate($perPage);
    }

    public function findById(int $id): ?SoldFromCompany
    {
        /** @var SoldFromCompany|null $company */
        $company = SoldFromCompany::find($id);
        return $company;
    }

    /**
     * @param array<string, mixed> $data
     * @return SoldFromCompany
     */
    public function create(array $data): SoldFromCompany
    {
        /** @var SoldFromCompany $company */
        $company = SoldFromCompany::create($data);
        return $company;
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $company = $this->findById($id);
        if (!$company) {
            return false;
        }
        return $company->update($data);
    }

    public function delete(int $id): bool
    {
        $company = $this->findById($id);
        if (!$company) {
            return false;
        }
        return (bool)$company->delete();
    }
}
