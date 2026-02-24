<?php

namespace Tests\Feature;

use App\Models\FinReport;
use App\Models\User;
use App\Models\Spk;
use App\Models\Supplier;
use App\Models\Nds;
use App\Models\SoldFromCompany;
use App\Models\Variable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinDirectorTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'finance']);
        $this->actingAs($this->user);
    }

    public function test_can_view_fin_reports_index(): void
    {
        FinReport::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->get(route('findirector.fin-reports.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.findirector.fin_reports.index');
        $response->assertViewHas('reports');
    }

    public function test_can_view_add_fin_report_page(): void
    {
        $response = $this->get(route('findirector.fin-reports.add'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.findirector.fin_reports.add');
    }

    public function test_can_store_fin_report(): void
    {
        $spk = Spk::factory()->create();
        $supplier = Supplier::factory()->create();
        $nds = Nds::factory()->create();

        $data = [
            'report_title' => 'Test Report',
            'amount' => 1000,
            'date' => now()->toDateString(),
            'spk_id' => $spk->id,
            'supplier_id' => $supplier->id,
            'nds_id' => $nds->id,
            'received_amount' => 1000,
        ];

        $response = $this->post(route('findirector.fin-reports.store'), $data);

        $response->assertRedirect(route('findirector.fin-reports.index'));
        $this->assertDatabaseHas('fin_reports', [
            'report_title' => 'Test Report',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_can_edit_own_fin_report(): void
    {
        $report = FinReport::factory()->create(['user_id' => $this->user->id]);

        $response = $this->get(route('findirector.fin-reports.edit', $report->id));

        $response->assertStatus(200);
        $response->assertViewIs('pages.findirector.fin_reports.edit');
    }

    public function test_cannot_edit_others_fin_report(): void
    {
        $otherUser = User::factory()->create();
        $report = FinReport::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->get(route('findirector.fin-reports.edit', $report->id));

        $response->assertStatus(403);
    }

    public function test_can_update_fin_report(): void
    {
        $report = FinReport::factory()->create(['user_id' => $this->user->id]);

        $data = [
            'report_title' => 'Updated Title',
            'amount' => 2000,
            'date' => now()->toDateString(),
        ];

        $response = $this->put(route('findirector.fin-reports.update', $report->id), $data);

        $response->assertRedirect(route('findirector.fin-reports.index'));
        $this->assertDatabaseHas('fin_reports', [
            'id' => $report->id,
            'report_title' => 'Updated Title',
        ]);
    }

    public function test_can_delete_fin_report(): void
    {
        $report = FinReport::factory()->create(['user_id' => $this->user->id]);

        $response = $this->post(route('findirector.fin-reports.delete', $report->id));

        $response->assertRedirect(route('findirector.fin-reports.index'));
        $this->assertDatabaseMissing('fin_reports', ['id' => $report->id]);
    }
}
