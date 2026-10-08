<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Prospect;
use App\Models\ProspectLockSetting;
use App\Models\ProspectSource;
use App\Models\ProspectStatus;
use App\Models\Role;
use App\Models\Sender;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProspectLockSettingTest extends TestCase
{
    use DatabaseTransactions;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function createCommonData(): array
    {
        $csRole = Role::firstOrCreate(['slug' => 'cs'], ['name' => 'CS']);
        $mgrRole = Role::firstOrCreate(['slug' => 'manager_marketing'], ['name' => 'Manager Marketing']);
        $adminRole = Role::firstOrCreate(['slug' => 'super_admin'], ['name' => 'Super Admin']);
        $mktRole = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);

        $cs = User::factory()->create(['role_id' => $csRole->id]);
        $manager = User::factory()->create(['role_id' => $mgrRole->id]);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $marketing = User::factory()->create(['role_id' => $mktRole->id]);

        $service = Service::firstOrCreate(['name' => 'PT']);
        $sender = Sender::firstOrCreate(['name' => 'Website']);
        $group = Group::firstOrCreate(['name' => 'Group A']);
        $source = ProspectSource::firstOrCreate(['name' => 'Google']);
        $status = ProspectStatus::firstOrCreate(['slug' => 'open'], ['name' => 'Open', 'color' => '#3B82F6']);

        return compact('cs', 'manager', 'admin', 'marketing', 'service', 'sender', 'group', 'source', 'status');
    }

    public function test_cs_cannot_access_create_and_store_prospect_after_10pm(): void
    {
        $data = $this->createCommonData();

        // 1. Set waktu ke jam 22:30 (lewat jam 10 malam)
        Carbon::setTestNow(Carbon::parse('2026-10-06 22:30:00'));

        // Cek instance setting otomatis terkunci
        $setting = ProspectLockSetting::instance();
        $this->assertTrue($setting->is_locked);

        // Akses create harus di-redirect ke index dengan pesan error
        $responseCreate = $this->actingAs($data['cs'])->get(route('prospects.create'));
        $responseCreate->assertRedirect(route('prospects.index'));
        $responseCreate->assertSessionHas('error');

        // Submit form store juga harus di-redirect ke index dan tidak ada data tersimpan
        $responseStore = $this->actingAs($data['cs'])->post(route('prospects.store'), [
            'client_phone' => '081299991111',
            'service_id' => $data['service']->id,
            'sender_id' => $data['sender']->id,
            'group_id' => $data['group']->id,
            'source_id' => $data['source']->id,
            'marketing_user_id' => $data['marketing']->id,
            'entry_date' => '2026-10-06',
            'entry_time' => '22:30',
        ]);

        $responseStore->assertRedirect(route('prospects.index'));
        $responseStore->assertSessionHas('error');

        $this->assertDatabaseMissing('prospects', [
            'client_phone' => '081299991111',
        ]);
    }

    public function test_cs_can_access_create_and_store_prospect_at_6am(): void
    {
        $data = $this->createCommonData();

        // Set waktu ke jam 06:05 pagi
        Carbon::setTestNow(Carbon::parse('2026-10-07 06:05:00'));

        $setting = ProspectLockSetting::instance();
        $this->assertFalse($setting->is_locked);

        // Akses create berhasil
        $responseCreate = $this->actingAs($data['cs'])->get(route('prospects.create'));
        $responseCreate->assertOk();
        $responseCreate->assertSee('Input Prospek Baru');

        // Submit form store berhasil
        $responseStore = $this->actingAs($data['cs'])->post(route('prospects.store'), [
            'client_phone' => '081299992222',
            'service_id' => $data['service']->id,
            'sender_id' => $data['sender']->id,
            'group_id' => $data['group']->id,
            'source_id' => $data['source']->id,
            'marketing_user_id' => $data['marketing']->id,
            'entry_date' => '2026-10-07',
            'entry_time' => '06:05',
        ]);

        $responseStore->assertRedirect(route('prospects.index'));
        $responseStore->assertSessionHas('status', 'Prospek berhasil dibuat.');

        $this->assertDatabaseHas('prospects', [
            'client_phone' => '081299992222',
        ]);
    }

    public function test_manager_can_manually_unlock_before_6am(): void
    {
        $data = $this->createCommonData();

        // Set waktu ke jam 05:00 pagi (sebelum jam 6 pagi, default terkunci)
        Carbon::setTestNow(Carbon::parse('2026-10-07 05:00:00'));

        $setting = ProspectLockSetting::instance();
        $this->assertTrue($setting->is_locked);

        // CS sebelum dibuka: tidak bisa create
        $resBefore = $this->actingAs($data['cs'])->get(route('prospects.create'));
        $resBefore->assertRedirect(route('prospects.index'));

        // Manager membuka kunci secara manual
        $resUnlock = $this->actingAs($data['manager'])->post(route('prospects.lock-toggle'));
        $resUnlock->assertRedirect();
        $resUnlock->assertSessionHas('status');

        $setting->refresh();
        $this->assertFalse($setting->is_locked);

        // Sekarang CS bisa create prospek meskipun masih jam 05:05
        Carbon::setTestNow(Carbon::parse('2026-10-07 05:05:00'));
        $resAfter = $this->actingAs($data['cs'])->get(route('prospects.create'));
        $resAfter->assertOk();

        $resStore = $this->actingAs($data['cs'])->post(route('prospects.store'), [
            'client_phone' => '081299993333',
            'service_id' => $data['service']->id,
            'sender_id' => $data['sender']->id,
            'group_id' => $data['group']->id,
            'source_id' => $data['source']->id,
            'marketing_user_id' => $data['marketing']->id,
            'entry_date' => '2026-10-07',
            'entry_time' => '05:05',
        ]);
        $resStore->assertRedirect(route('prospects.index'));
        $this->assertDatabaseHas('prospects', ['client_phone' => '081299993333']);
    }

    public function test_manager_can_manually_lock_before_10pm(): void
    {
        $data = $this->createCommonData();

        // Set waktu ke jam 20:00 (sebelum jam 10 malam, default terbuka)
        Carbon::setTestNow(Carbon::parse('2026-10-06 20:00:00'));

        $setting = ProspectLockSetting::instance();
        $this->assertFalse($setting->is_locked);

        // Manager mengunci secara manual
        $resLock = $this->actingAs($data['manager'])->post(route('prospects.lock-toggle'), [
            'reason' => 'Tutup input awal untuk evaluasi data',
        ]);
        $resLock->assertRedirect();
        $resLock->assertSessionHas('status');

        $setting->refresh();
        $this->assertTrue($setting->is_locked);

        // Sekarang CS diblokir dari create
        $resCs = $this->actingAs($data['cs'])->get(route('prospects.create'));
        $resCs->assertRedirect(route('prospects.index'));
        $resCs->assertSessionHas('error');
    }

    public function test_prospects_index_ui_elements_for_cs_and_manager(): void
    {
        $data = $this->createCommonData();

        // Jam 22:30 (terkunci)
        Carbon::setTestNow(Carbon::parse('2026-10-06 22:30:00'));

        // CS lihat notifikasi prospek terkunci
        $resCs = $this->actingAs($data['cs'])->get(route('prospects.index'));
        $resCs->assertOk();
        $resCs->assertSee('Input Prospek Baru Sedang Dikunci');
        $resCs->assertSee('Prospek Dikunci');

        // Manager lihat tombol Buka Kunci Prospek
        $resMgr = $this->actingAs($data['manager'])->get(route('prospects.index'));
        $resMgr->assertOk();
        $resMgr->assertSee('Status Input Prospek CS:');
        $resMgr->assertSee('Buka Kunci Prospek');

        // Super Admin juga lihat tombol Buka Kunci Prospek
        $resAdmin = $this->actingAs($data['admin'])->get(route('prospects.index'));
        $resAdmin->assertOk();
        $resAdmin->assertSee('Status Input Prospek CS:');
        $resAdmin->assertSee('Buka Kunci Prospek');
    }
}
