<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Prospect;
use App\Models\ProspectStatus;
use App\Models\Service;
use App\Models\Sender;
use App\Models\Group;
use App\Models\ProspectSource;
use App\Models\Todo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use ZipArchive;

class ManagerTodoExportZipTest extends TestCase
{
    use DatabaseTransactions;

    public function test_manager_can_export_all_marketings_as_zip(): void
    {
        $managerRole = Role::firstOrCreate(['slug' => 'manager_marketing'], ['name' => 'Manager Marketing']);
        $marketingRole = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);

        $manager = User::factory()->create(['role_id' => $managerRole->id]);
        $marketing1 = User::factory()->create(['name' => 'Budi Santoso', 'role_id' => $marketingRole->id]);
        $marketing2 = User::factory()->create(['name' => 'Siti Aminah', 'role_id' => $marketingRole->id]);

        $response = $this->actingAs($manager)->get(route('manager.todos.export', [
            'month' => 9,
            'year' => 2026,
        ]));

        $response->assertOk();
        $this->assertStringContainsString('application/zip', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.zip', $response->headers->get('Content-Disposition'));

        // Simpan binary response ke temp file dan inspect isi zip
        $tempZip = tempnam(sys_get_temp_dir(), 'test_zip_') . '.zip';
        file_put_contents($tempZip, $response->streamedContent());

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($tempZip));
        $this->assertGreaterThanOrEqual(2, $zip->numFiles);

        $fileNames = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $fileNames[] = $zip->getNameIndex($i);
        }
        $zip->close();
        @unlink($tempZip);

        $this->assertTrue(collect($fileNames)->contains(fn ($name) => str_contains($name, 'Budi_Santoso') && str_ends_with($name, '.xlsx')));
        $this->assertTrue(collect($fileNames)->contains(fn ($name) => str_contains($name, 'Siti_Aminah') && str_ends_with($name, '.xlsx')));
    }

    public function test_manager_can_export_single_marketing_as_xlsx(): void
    {
        $managerRole = Role::firstOrCreate(['slug' => 'manager_marketing'], ['name' => 'Manager Marketing']);
        $marketingRole = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);

        $manager = User::factory()->create(['role_id' => $managerRole->id]);
        $marketing = User::factory()->create(['name' => 'Ahmad Dahlan', 'role_id' => $marketingRole->id]);

        $response = $this->actingAs($manager)->get(route('manager.todos.export', [
            'month' => 9,
            'year' => 2026,
            'user_id' => $marketing->id,
        ]));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml.sheet', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Rekap_Todos_Ahmad_Dahlan', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('Content-Disposition'));
    }
}
