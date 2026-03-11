<?php

namespace App\Repositories;

use App\Models\FinReport;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<FinReport>
 */
class FinReportRepository extends BaseRepository
{
    public function __construct(FinReport $model)
    {
        parent::__construct($model);
    }

    /**
     * Get paginated reports for a specific user with optional filters.
     *
     * @param int $userId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator<FinReport>
     */
    public function getPaginatedForUser(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->with(['spkPerson', 'supplier', 'nds'])
            ->where('user_id', $userId);

        if (!empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }

        if (!empty($filters['manager'])) {
            $query->where('manager_name', 'like', '%' . $filters['manager'] . '%');
        }

        if (!empty($filters['supplier'])) {
            $query->whereHas('supplier', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['supplier'] . '%');
            });
        }

        return $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Find a report by ID and ensure it belongs to the user.
     *
     * @param int $id
     * @param int $userId
     * @return FinReport|null
     */
    public function findForUser(int $id, int $userId): ?FinReport
    {
        /** @var FinReport|null $result */
        $result = $this->model->where('id', $id)
            ->where('user_id', $userId)
            ->first();
        return $result;
    }
}
