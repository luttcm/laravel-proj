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
     * @param string $modelClass
     * @param int $managerId
     * @return Collection
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
     * @param string $modelClass
     * @param int $id
     * @param int $managerId
     * @return Model|null
     */
    public function findForManager(string $modelClass, int $id, int $managerId): ?Model
    {
        return $modelClass::where('id', $id)
            ->where('manager_id', $managerId)
            ->first();
    }

    /**
     * Create a new report.
     *
     * @param string $modelClass
     * @param array $data
     * @return Model
     */
    public function create(string $modelClass, array $data): Model
    {
        return $modelClass::create($data);
    }
}
