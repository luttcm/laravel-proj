<?php

namespace Tests\Unit;

use App\Models\FinReport;
use App\Models\User;
use App\Repositories\FinReportRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinReportRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new FinReportRepository(new FinReport());
    }

    public function test_getPaginatedForUser_returns_only_user_reports(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        FinReport::factory()->count(3)->create(['user_id' => $user->id]);
        FinReport::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $results = $this->repository->getPaginatedForUser($user->id);

        $this->assertEquals(3, $results->total());
        foreach ($results as $report) {
            $this->assertEquals($user->id, $report->user_id);
        }
    }

    public function test_findForUser_returns_report_belonging_to_user(): void
    {
        $user = User::factory()->create();
        $report = FinReport::factory()->create(['user_id' => $user->id]);

        $found = $this->repository->findForUser($report->id, $user->id);

        $this->assertNotNull($found);
        $this->assertEquals($report->id, $found->id);
    }

    public function test_findForUser_returns_null_for_other_user_report(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $report = FinReport::factory()->create(['user_id' => $otherUser->id]);

        $found = $this->repository->findForUser($report->id, $user->id);

        $this->assertNull($found);
    }
}
