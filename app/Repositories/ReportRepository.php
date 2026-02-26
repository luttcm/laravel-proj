<?php

namespace App\Repositories;

use App\Models\Reports;
use App\Models\DraftsReports;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ReportRepository
{
    /**
     * Get all reports for a specific manager, ordered by creation date.
     *
     * @param class-string<Model> $modelClass
     * @param int $managerId
     * @return Collection<int, Model>
     */
    public function getForManager(string $modelClass, int $managerId): Collection
    {
        return $modelClass::where('manager_id', $managerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find a report by ID and ensure it belongs to the manager.
     *
     * @param class-string<Model> $modelClass
     * @param int $id
     * @param int $managerId
     * @return Model|null
     */
    public function findForManager(string $modelClass, int $id, int $managerId): ?Model
    {
        /** @var Model|null $result */
        $result = $modelClass::where('id', $id)
            ->where('manager_id', $managerId)
            ->first();
        return $result;
    }

    /**
     * Create a new report.
     *
     * @param class-string<Model> $modelClass
     * @param array<string, mixed> $data
     * @return Model
     */
    public function create(string $modelClass, array $data): Model
    {
        /** @var Model $result */
        $result = $modelClass::create($data);
        return $result;
    }
}
