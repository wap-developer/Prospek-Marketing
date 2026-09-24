<?php

namespace Tests\Feature;

use App\Models\Prospect;
use App\Models\ProspectStatus;
use App\Models\ProspectWeeklyUpdate;
use App\Models\Role;
use App\Models\Sender;
use App\Models\Service;
use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ManagerTodoTask7ComplianceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_task_7_is_not_marked_x_when_marketing_has_no_prospects(): void
    {
        $managerRole = Role::firstOrCreate(['slug' => 'manager_marketing'], ['name' => 'Manager Marketing']);
        $marketingRole = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);

        $manager = User::factory()->create(['role_id' => $managerRole->id]);
        $marketing = User::factory()->create(['role_id' => $marketingRole->id]);

        // Pastikan tidak ada prospek on-process untuk marketing ini di bulan September 2026
        Prospect::where('marketing_user_id', $marketing->id)
            ->whereMonth('entry_date', 9)
            ->whereYear('entry_date', 2026)
            ->delete();

        // Bahkan jika ada catatan lama di tabel todos
        Todo::create([
            'user_id' => $marketing->id,
            'date' => '2026-09-14',
            'prospect_progress_note' => 'Catatan lama yang tidak boleh memicu X',
        ]);

        $response = $this->actingAs($manager)->get(route('manager.todos', [
            'month' => 9,
            'year' => 2026,
        ]));

        $response->assertOk();

        $matrixData = $response->viewData('matrixData');
        $userMatrix = $matrixData->firstWhere('user.id', $marketing->id);

        $this->assertNotNull($userMatrix);

        // Seluruh hari di bulan ini untuk task 7 harus bernilai false
        foreach ($userMatrix['dailyTasks'] as $day => $tasks) {
            $this->assertFalse($tasks[7], "Day {$day} Task 7 should not be true when there are no prospects.");
        }
    }

    public function test_task_7_is_marked_x_only_on_date_sent_when_all_prospects_sent_on_that_date_are_updated(): void
    {
        $managerRole = Role::firstOrCreate(['slug' => 'manager_marketing'], ['name' => 'Manager Marketing']);
        $marketingRole = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);
        $openStatus = ProspectStatus::firstOrCreate(['slug' => 'open'], ['name' => 'On Process', 'color' => '#3B82F6']);
        $service = Service::firstOrCreate(['name' => 'PT']);
        $sender = Sender::firstOrCreate(['name' => 'Website']);
        $group = \App\Models\Group::firstOrCreate(['name' => 'Group A']);
        $source = \App\Models\ProspectSource::firstOrCreate(['name' => 'Google']);

        $manager = User::factory()->create(['role_id' => $managerRole->id]);
        $marketing = User::factory()->create(['role_id' => $marketingRole->id]);

        // Prospek 1 dikirim CS pada tanggal 21
        $prospectDay21 = Prospect::create([
            'client_phone' => '081234567890',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'source_id' => $source->id,
            'marketing_user_id' => $marketing->id,
            'status_id' => $openStatus->id,
            'entry_date' => '2026-09-21',
            'entry_time' => '10:00:00',
            'created_by' => $manager->id,
        ]);

        // Prospek 2 dikirim CS pada tanggal 22
        $prospectDay22 = Prospect::create([
            'client_phone' => '081234567891',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'source_id' => $source->id,
            'marketing_user_id' => $marketing->id,
            'status_id' => $openStatus->id,
            'entry_date' => '2026-09-22',
            'entry_time' => '11:00:00',
            'created_by' => $manager->id,
        ]);

        // 1. Belum ada catatan perkembangan diisi: Todo 7 pada tgl 21 dan 22 harus false (tidak X)
        $res1 = $this->actingAs($manager)->get(route('manager.todos', ['month' => 9, 'year' => 2026]));
        $matrix1 = $res1->viewData('matrixData')->firstWhere('user.id', $marketing->id);

        $this->assertFalse($matrix1['dailyTasks'][21][7], 'Day 21 should NOT be X before update');
        $this->assertFalse($matrix1['dailyTasks'][22][7], 'Day 22 should NOT be X before update');
        $this->assertFalse($matrix1['dailyTasks'][1][7], 'Day 1 without prospects should NOT be X');

        // 2. Marketing update catatan untuk prospek tanggal 21
        $prospectDay21->update(['note' => 'Sudah ditelepon klien tertarik']);

        $res2 = $this->actingAs($manager)->get(route('manager.todos', ['month' => 9, 'year' => 2026]));
        $matrix2 = $res2->viewData('matrixData')->firstWhere('user.id', $marketing->id);

        // Todo 7 pada tanggal 21 sekarang harus berubah menjadi X (true), sedangkan tanggal 22 tetap false
        $this->assertTrue($matrix2['dailyTasks'][21][7], 'Day 21 MUST become X after prospect sent on day 21 is updated');
        $this->assertFalse($matrix2['dailyTasks'][22][7], 'Day 22 must remain false until updated');

        // 3. Marketing update catatan untuk prospek tanggal 22 via ProspectWeeklyUpdate
        ProspectWeeklyUpdate::create([
            'prospect_id' => $prospectDay22->id,
            'user_id' => $marketing->id,
            'year' => 2026,
            'iso_week' => 39,
            'month' => 9,
            'week_of_month' => 4,
            'note' => 'Follow up WA hari ini deal',
        ]);

        $res3 = $this->actingAs($manager)->get(route('manager.todos', ['month' => 9, 'year' => 2026]));
        $matrix3 = $res3->viewData('matrixData')->firstWhere('user.id', $marketing->id);

        // Keduanya sekarang harus X (true) pada tanggal pengirimannya masing-masing
        $this->assertTrue($matrix3['dailyTasks'][21][7], 'Day 21 must be X');
        $this->assertTrue($matrix3['dailyTasks'][22][7], 'Day 22 must be X');
        $this->assertFalse($matrix3['dailyTasks'][23][7], 'Day 23 without prospects must remain false');
    }
}
