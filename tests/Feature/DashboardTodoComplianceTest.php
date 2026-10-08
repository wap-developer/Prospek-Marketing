<?php

namespace Tests\Feature;

use App\Models\Prospect;
use App\Models\ProspectStatus;
use App\Models\ProspectWeeklyUpdate;
use App\Models\Role;
use App\Models\Sender;
use App\Models\Service;
use App\Models\Todo;
use App\Models\TodoLink;
use App\Models\TodoPdf;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DashboardTodoComplianceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_manager_dashboard_shows_7_of_7_when_marketing_completes_all_tasks(): void
    {
        $managerRole = Role::firstOrCreate(['slug' => 'manager_marketing'], ['name' => 'Manager Marketing']);
        $marketingRole = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);
        $openStatus = ProspectStatus::firstOrCreate(['slug' => 'open'], ['name' => 'Open', 'color' => '#3B82F6']);
        $service = Service::firstOrCreate(['name' => 'PT']);
        $sender = Sender::firstOrCreate(['name' => 'Website']);
        $group = \App\Models\Group::firstOrCreate(['name' => 'Group A']);
        $source = \App\Models\ProspectSource::firstOrCreate(['name' => 'Google']);

        $manager = User::factory()->create(['role_id' => $managerRole->id]);
        $marketing = User::factory()->create(['role_id' => $marketingRole->id]);

        $today = today();

        // 1. Buat todo hari ini dengan 12 link & 5 file PDF tugas 2-6
        $todo = Todo::create([
            'user_id' => $marketing->id,
            'date' => $today->toDateString(),
        ]);

        $platforms = ['instagram', 'tiktok', 'facebook', 'snack_video'];
        foreach ($platforms as $platform) {
            for ($slot = 1; $slot <= 3; $slot++) {
                TodoLink::create([
                    'todo_id' => $todo->id,
                    'platform' => $platform,
                    'slot' => $slot,
                    'url' => "https://example.com/{$platform}/{$slot}",
                ]);
            }
        }

        for ($task = 2; $task <= 6; $task++) {
            TodoPdf::create([
                'todo_id' => $todo->id,
                'task' => $task,
                'file_path' => "todos/{$todo->id}/task_{$task}.pdf",
                'original_name' => "task_{$task}.pdf",
            ]);
        }

        // 2. Buat prospek hari ini yang dikirim ke marketing tersebut
        $prospect = Prospect::create([
            'client_phone' => '081299990001',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'source_id' => $source->id,
            'marketing_user_id' => $marketing->id,
            'status_id' => $openStatus->id,
            'entry_date' => $today->toDateString(),
            'entry_time' => '09:00:00',
            'created_by' => $manager->id,
        ]);

        // Cek dashboard sebelum prospek diupdate: harusnya 6 / 7
        $resBefore = $this->actingAs($manager)->get(route('dashboard'));
        $resBefore->assertOk();
        $complianceBefore = $resBefore->viewData('todoComplianceToday');
        $marketingRowBefore = $complianceBefore->firstWhere('name', $marketing->name);
        $this->assertNotNull($marketingRowBefore);
        $this->assertEquals(6, $marketingRowBefore['tasks_done']);
        $this->assertFalse($marketingRowBefore['is_complete']);

        // 3. Marketing update catatan prospek tersebut
        $prospect->update(['note' => 'Sudah difollowup hari ini deal']);

        // Cek dashboard setelah prospek diupdate: harus 7 / 7 dan is_complete true
        $resAfter = $this->actingAs($manager)->get(route('dashboard'));
        $resAfter->assertOk();
        $complianceAfter = $resAfter->viewData('todoComplianceToday');
        $marketingRowAfter = $complianceAfter->firstWhere('name', $marketing->name);
        $this->assertNotNull($marketingRowAfter);
        $this->assertEquals(7, $marketingRowAfter['tasks_done']);
        $this->assertTrue($marketingRowAfter['is_complete']);
        $this->assertTrue($marketingRowAfter['has_note']);

        // Cek juga di dashboard marketing (role marketing)
        $resMarketing = $this->actingAs($marketing)->get(route('dashboard'));
        $resMarketing->assertOk();
        $stats = $resMarketing->viewData('todayTodoStats');
        $this->assertNotNull($stats);
        $this->assertEquals(7, $stats['tasks_done']);
        $this->assertEquals('ok', $stats['state']);
    }
}
