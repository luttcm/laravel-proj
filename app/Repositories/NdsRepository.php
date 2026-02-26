<?php

namespace App\Repositories;

use App\Models\Nds;
use Illuminate\Database\Eloquent\Collection;

class NdsRepository
{
    /**
     * @return Collection<int, Nds>
     */
    public function getAll(): Collection
    {
        return Nds::all();
    }

    /**
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator<Nds>
     */
    public function getAllPaginated(int $perPage = 10): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Nds::paginate($perPage);
    }

    public function findById(int $id): ?Nds
    {
        /** @var Nds|null $nds */
        $nds = Nds::find($id);
        return $nds;
    }

    /**
     * @param array<string, mixed> $data
     * @return Nds
     */
    public function create(array $data): Nds
    {
        /** @var Nds $nds */
        $nds = Nds::create($data);
        return $nds;
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $nds = $this->findById($id);
        if (!$nds) {
            return false;
        }
        return $nds->update($data);
    }

    public function delete(int $id): bool
    {
        $nds = $this->findById($id);
        if (!$nds) {
            return false;
        }
        return (bool)$nds->delete();
    }
}
