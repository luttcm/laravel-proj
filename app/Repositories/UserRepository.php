<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    /**
     * @return Collection<int, User>
     */
    public function getAll(): Collection
    {
        return User::all();
    }

    public function findById(int $id): ?User
    {
        return User::findOrFail($id);
    }

    /**
     * @param array<string, mixed> $data
     * @return User
     */
    public function create(array $data): User
    {
        /** @var User $user */
        $user = User::create($data);
        return $user;
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $user = $this->findById($id);
        /** @var User $user */
        return $user->update($data);
    }

    public function delete(int $id): bool
    {
        $user = $this->findById($id);
        /** @var User $user */
        return (bool)$user->delete();
    }
}
