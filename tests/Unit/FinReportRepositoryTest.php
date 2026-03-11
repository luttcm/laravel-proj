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

    public function test_getPaginatedForUser_with_filters(): void
    {
        $user = User::factory()->create();
        $supplierA = \App\Models\Supplier::factory()->create(['name' => 'Supplier A']);
        $supplierB = \App\Models\Supplier::factory()->create(['name' => 'Supplier B']);

        FinReport::factory()->create([
            'user_id' => $user->id,
            'date' => '2023-01-01',
            'manager_name' => 'John Doe',
            'supplier_id' => $supplierA->id,
        ]);

        FinReport::factory()->create([
            'user_id' => $user->id,
            'date' => '2023-01-02',
            'manager_name' => 'Jane Smith',
            'supplier_id' => $supplierB->id,
        ]);

        // Filter by date
        $results = $this->repository->getPaginatedForUser($user->id, ['date' => '2023-01-01']);
        $this->assertEquals(1, $results->total());
        $this->assertEquals('John Doe', $results->first()->manager_name);

        // Filter by manager (partial)
        $results = $this->repository->getPaginatedForUser($user->id, ['manager' => 'Jane']);
        $this->assertEquals(1, $results->total());
        $this->assertEquals('Jane Smith', $results->first()->manager_name);

        // Filter by supplier (partial)
        $results = $this->repository->getPaginatedForUser($user->id, ['supplier' => 'Supplier A']);
        $this->assertEquals(1, $results->total());
        $this->assertEquals('John Doe', $results->first()->manager_name);

        // Mixed filters
        $results = $this->repository->getPaginatedForUser($user->id, ['date' => '2023-01-01', 'manager' => 'Jane']);
        $this->assertEquals(0, $results->total());
    }
}
