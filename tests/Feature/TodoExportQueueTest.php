<?php

namespace Tests\Feature;

use App\Jobs\ExportTodosJob;
use App\Models\Role;
use App\Models\TodoExport;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use ZipArchive;

class TodoExportQueueTest extends TestCase
{
    use DatabaseTransactions;

    public function test_manager_can_start_async_export(): void
    {
        Queue::fake();

        $managerRole = Role::firstOrCreate(['slug' => 'manager_marketing'], ['name' => 'Manager Marketing']);
        $marketingRole = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);

        $manager = User::factory()->create(['role_id' => $managerRole->id]);
        $marketing = User::factory()->create(['name' => 'Test Marketing', 'role_id' => $marketingRole->id]);

        $response = $this->actingAs($manager)->postJson(route('manager.todos.export.start'), [
            'month' => 9,
            'year' => 2026,
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'percent' => 0,
            ])
            ->assertJsonStructure(['export_id', 'status', 'total', 'processed', 'message']);

        $exportId = $response->json('export_id');
        $this->assertDatabaseHas('todo_exports', [
            'id' => $exportId,
            'user_id' => $manager->id,
            'month' => 9,
            'year' => 2026,
            'status' => 'pending',
        ]);

        Queue::assertPushed(ExportTodosJob::class, function ($job) use ($exportId) {
            return $job->export->id === $exportId;
        });
    }

    public function test_export_status_and_recent_endpoints_work(): void
    {
        $managerRole = Role::firstOrCreate(['slug' => 'manager_marketing'], ['name' => 'Manager Marketing']);
        $manager = User::factory()->create(['role_id' => $managerRole->id]);

        $export = TodoExport::create([
            'user_id' => $manager->id,
            'month' => 9,
            'year' => 2026,
            'status' => 'processing',
            'total_marketing' => 10,
            'processed_marketing' => 5,
        ]);

        $statusResponse = $this->actingAs($manager)->getJson(route('manager.todos.export.status', $export));
        $statusResponse->assertOk()
            ->assertJson([
                'id' => $export->id,
                'status' => 'processing',
                'percent' => 50,
                'processed' => 5,
                'total' => 10,
            ]);

        $recentResponse = $this->actingAs($manager)->getJson(route('manager.todos.export.recent'));
        $recentResponse->assertOk()
            ->assertJsonStructure(['exports']);
        $this->assertTrue(collect($recentResponse->json('exports'))->contains(fn ($e) => $e['id'] === $export->id));
    }

    public function test_export_todos_job_executes_and_generates_valid_zip(): void
    {
        $managerRole = Role::firstOrCreate(['slug' => 'manager_marketing'], ['name' => 'Manager Marketing']);
        $marketingRole = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);

        $manager = User::factory()->create(['role_id' => $managerRole->id]);
        $marketing1 = User::factory()->create(['name' => 'Job Mkt 1', 'role_id' => $marketingRole->id]);
        $marketing2 = User::factory()->create(['name' => 'Job Mkt 2', 'role_id' => $marketingRole->id]);

        $export = TodoExport::create([
            'user_id' => $manager->id,
            'month' => 9,
            'year' => 2026,
            'status' => 'pending',
            'total_marketing' => 2,
            'processed_marketing' => 0,
        ]);

        $job = new ExportTodosJob($export);
        $job->handle();

        $export->refresh();
        $this->assertEquals('completed', $export->status);
        $this->assertEquals(100, $export->percent);
        $this->assertNotNull($export->file_path);
        $this->assertFileExists($export->file_path);

        // Verifikasi isi file zip yang digenerate oleh job
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($export->file_path));
        $this->assertGreaterThanOrEqual(2, $zip->numFiles);
        $fileNames = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $fileNames[] = $zip->getNameIndex($i);
        }
        $zip->close();

        $this->assertTrue(collect($fileNames)->contains(fn ($n) => str_contains($n, 'Job_Mkt_1') && str_ends_with($n, '.xlsx')));
        $this->assertTrue(collect($fileNames)->contains(fn ($n) => str_contains($n, 'Job_Mkt_2') && str_ends_with($n, '.xlsx')));

        // Test download route
        $downloadResponse = $this->actingAs($manager)->get(route('manager.todos.export.download', $export));
        $downloadResponse->assertOk();
        $this->assertStringContainsString('application/zip', $downloadResponse->headers->get('Content-Type'));

        // Cleanup generated test zip file
        @unlink($export->file_path);
    }
}
