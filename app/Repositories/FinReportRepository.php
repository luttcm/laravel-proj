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
     * Get paginated reports for a specific user.
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator<FinReport>
     */
    public function getPaginatedForUser(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->with(['spkPerson', 'supplier', 'nds'])
            ->where('user_id', $userId)
            ->orderBy('date', 'desc')
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
