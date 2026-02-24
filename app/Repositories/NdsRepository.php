<?php

namespace App\Repositories;

use App\Models\Nds;
use Illuminate\Database\Eloquent\Collection;

class NdsRepository
{
    public function getAll(): Collection
    {
        return Nds::all();
    }

    public function getAllPaginated(int $perPage = 10)
    {
        return Nds::paginate($perPage);
    }

    public function findById(int $id): ?Nds
    {
        return Nds::findOrFail($id);
    }

    public function create(array $data): Nds
    {
        return Nds::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $nds = $this->findById($id);
        return $nds->update($data);
    }

    public function delete(int $id): bool
    {
        $nds = $this->findById($id);
        return $nds->delete();
    }
}
