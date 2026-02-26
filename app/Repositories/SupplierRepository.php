<?php

namespace App\Repositories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;

class SupplierRepository
{
    /**
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator<\App\Models\Supplier>
     */
    public function getAllPaginated(int $perPage = 10): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Supplier::paginate($perPage);
    }

    public function findById(int $id): ?Supplier
    {
        /** @var Supplier|null $supplier */
        $supplier = Supplier::find($id);
        return $supplier;
    }

    /**
     * @param array<string, mixed> $data
     * @return Supplier
     */
    public function create(array $data): Supplier
    {
        /** @var Supplier $supplier */
        $supplier = Supplier::create($data);
        return $supplier;
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $supplier = $this->findById($id);
        if (!$supplier) {
            return false;
        }
        return $supplier->update($data);
    }

    public function delete(int $id): bool
    {
        $supplier = $this->findById($id);
        if (!$supplier) {
            return false;
        }
        return (bool)$supplier->delete();
    }
}
