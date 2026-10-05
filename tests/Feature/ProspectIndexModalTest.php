<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProspectIndexModalTest extends TestCase
{
    use DatabaseTransactions;

    public function test_authenticated_user_can_see_prospects_with_detail_modal(): void
    {
        $role = Role::firstOrCreate(
            ['slug' => 'manager_marketing'],
            ['name' => 'Manager Marketing']
        );

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $service = \App\Models\Service::firstOrCreate(['name' => 'PT']);
        $sender = \App\Models\Sender::firstOrCreate(['name' => 'Website']);
        $group = \App\Models\Group::firstOrCreate(['name' => 'Group A']);
        $source = \App\Models\ProspectSource::firstOrCreate(['name' => 'Google']);
        $status = \App\Models\ProspectStatus::firstOrCreate(['slug' => 'open'], ['name' => 'On Process', 'color' => '#3B82F6']);

        \App\Models\Prospect::create([
            'client_phone' => '081234567890',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'source_id' => $source->id,
            'marketing_user_id' => $user->id,
            'status_id' => $status->id,
            'entry_date' => now()->toDateString(),
            'entry_time' => now()->toTimeString(),
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('prospects.index'));

        $response->assertOk();
        $response->assertSee('prospectDetailModal');
        $response->assertSee('openProspectDetail');
        $response->assertSee('Lihat Detail Prospek');
        $response->assertSee('Keterangan Prospek');
        $response->assertSee('Catatan Prospek (Perkembangan Todo 7)');
    }

    public function test_cs_can_create_prospect_without_source_and_it_defaults_to_dash(): void
    {
        $csRole = Role::firstOrCreate(['slug' => 'cs'], ['name' => 'Customer Service']);
        $mktRole = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);

        $csUser = User::factory()->create(['role_id' => $csRole->id]);
        $mktUser = User::factory()->create(['role_id' => $mktRole->id]);

        $service = \App\Models\Service::firstOrCreate(['name' => 'PT']);
        $sender = \App\Models\Sender::firstOrCreate(['name' => 'Website']);
        $group = \App\Models\Group::firstOrCreate(['name' => 'Group A']);

        // POST prospect tanpa source_id
        $response = $this->actingAs($csUser)->post(route('prospects.store'), [
            'client_phone' => '089876543210',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'source_id' => '', // kosong
            'marketing_user_id' => $mktUser->id,
            'entry_date' => now()->toDateString(),
            'entry_time' => '10:00',
        ]);

        $response->assertRedirect(route('prospects.index'));

        $prospect = \App\Models\Prospect::where('client_phone', '089876543210')->first();
        $this->assertNotNull($prospect);
        $this->assertEquals('-', $prospect->source?->name);

        // Verifikasi di halaman index tampil strip '-'
        $indexRes = $this->actingAs($csUser)->get(route('prospects.index'));
        $indexRes->assertOk();
        $indexRes->assertSee('089876543210');
    }

    public function test_updating_prospect_with_dotted_nominal_closing_sanitizes_properly(): void
    {
        $managerRole = Role::firstOrCreate(['slug' => 'manager_marketing'], ['name' => 'Manager Marketing']);
        $manager = User::factory()->create(['role_id' => $managerRole->id]);

        $service = \App\Models\Service::firstOrCreate(['name' => 'PT']);
        $sender = \App\Models\Sender::firstOrCreate(['name' => 'Website']);
        $group = \App\Models\Group::firstOrCreate(['name' => 'Group A']);
        $closingStatus = \App\Models\ProspectStatus::firstOrCreate(['slug' => 'closing'], ['name' => 'Closing', 'color' => '#10B981']);

        $prospect = \App\Models\Prospect::create([
            'client_phone' => '081233334444',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'source_id' => null,
            'marketing_user_id' => $manager->id,
            'status_id' => $closingStatus->id,
            'entry_date' => now()->toDateString(),
            'entry_time' => '09:00:00',
            'created_by' => $manager->id,
        ]);

        $response = $this->actingAs($manager)->put(route('prospects.update', $prospect), [
            'status_id' => $closingStatus->id,
            'nominal_closing' => '7.500.000',
            'closed_at' => now()->format('Y-m-d H:i'),
        ]);

        $response->assertRedirect(route('prospects.edit', $prospect));

        $prospect->refresh();
        $this->assertEquals(7500000.0, (float) $prospect->nominal_closing);

        // Pastikan halaman edit merender nominal dengan format titik
        $editRes = $this->actingAs($manager)->get(route('prospects.edit', $prospect));
        $editRes->assertOk();
        $editRes->assertSee('7.500.000');
    }

    public function test_updating_prospect_to_cancel_status_deletes_prospect_immediately(): void
    {
        $managerRole = Role::firstOrCreate(['slug' => 'manager_marketing'], ['name' => 'Manager Marketing']);
        $manager = User::factory()->create(['role_id' => $managerRole->id]);

        $service = \App\Models\Service::firstOrCreate(['name' => 'PT']);
        $sender = \App\Models\Sender::firstOrCreate(['name' => 'Website']);
        $group = \App\Models\Group::firstOrCreate(['name' => 'Group A']);
        $openStatus = \App\Models\ProspectStatus::firstOrCreate(['slug' => 'open'], ['name' => 'On Process', 'color' => '#3B82F6']);
        $cancelStatus = \App\Models\ProspectStatus::firstOrCreate(['slug' => 'cancel'], ['name' => 'Cancel', 'color' => '#EF4444']);

        $prospect = \App\Models\Prospect::create([
            'client_phone' => '089999888877',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'marketing_user_id' => $manager->id,
            'status_id' => $openStatus->id,
            'entry_date' => now()->toDateString(),
            'entry_time' => '10:00:00',
            'created_by' => $manager->id,
        ]);

        $response = $this->actingAs($manager)->put(route('prospects.update', $prospect), [
            'status_id' => $cancelStatus->id,
        ]);

        $response->assertRedirect(route('prospects.index'));
        $this->assertDatabaseMissing('prospects', [
            'id' => $prospect->id,
            'client_phone' => '089999888877',
        ]);
    }

    public function test_manager_dashboard_renders_without_cancel_statistics(): void
    {
        $managerRole = Role::firstOrCreate(['slug' => 'manager_marketing'], ['name' => 'Manager Marketing']);
        $manager = User::factory()->create(['role_id' => $managerRole->id]);

        $response = $this->actingAs($manager)->get(route('dashboard'));

        $response->assertOk();
        $response->assertDontSee('Total Cancel');
        $response->assertDontSee('Cancellation Rate');
        $response->assertSee('Conversion Rate (Closing)');
    }

    public function test_cannot_create_prospect_with_duplicate_phone_number_and_shows_marketing_name(): void
    {
        $csRole = Role::firstOrCreate(['slug' => 'cs'], ['name' => 'Customer Service']);
        $mktRole = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);

        $csUser = User::factory()->create(['role_id' => $csRole->id]);
        $mktUser = User::factory()->create(['name' => 'Budi Santoso', 'role_id' => $mktRole->id]);

        $service = \App\Models\Service::firstOrCreate(['name' => 'Pendirian PT']);
        $sender = \App\Models\Sender::firstOrCreate(['name' => 'Website']);
        $group = \App\Models\Group::firstOrCreate(['name' => 'Group A']);
        $status = \App\Models\ProspectStatus::firstOrCreate(['slug' => 'open'], ['name' => 'On Process', 'color' => '#3B82F6']);

        $existingProspect = \App\Models\Prospect::create([
            'client_phone' => '081234567899',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'status_id' => $status->id,
            'marketing_user_id' => $mktUser->id,
            'entry_date' => now()->toDateString(),
            'entry_time' => '09:00:00',
            'created_by' => $mktUser->id,
        ]);
        $existingProspect->created_at = now()->subMinutes(5);
        $existingProspect->save();

        // Submit nomor sama persis
        $response = $this->actingAs($csUser)->post(route('prospects.store'), [
            'client_phone' => '081234567899',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'marketing_user_id' => $mktUser->id,
            'entry_date' => now()->toDateString(),
            'entry_time' => '10:00',
        ]);

        $response->assertSessionHasErrors('client_phone');
        $errorMsg = session('errors')->first('client_phone');
        $this->assertStringContainsString('Nomor prospek ini sudah ada di marketing Budi Santoso', $errorMsg);

        // Submit varian 62 (6281234567899)
        $responseVarian = $this->actingAs($csUser)->post(route('prospects.store'), [
            'client_phone' => '6281234567899',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'marketing_user_id' => $mktUser->id,
            'entry_date' => now()->toDateString(),
            'entry_time' => '10:00',
        ]);

        $responseVarian->assertSessionHasErrors('client_phone');
        $errorMsgVarian = session('errors')->first('client_phone');
        $this->assertStringContainsString('Nomor prospek ini sudah ada di marketing Budi Santoso', $errorMsgVarian);
    }

    public function test_check_phone_endpoint_returns_duplicate_status_and_marketing_name(): void
    {
        $csRole = Role::firstOrCreate(['slug' => 'cs'], ['name' => 'Customer Service']);
        $mktRole = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);

        $csUser = User::factory()->create(['role_id' => $csRole->id]);
        $mktUser = User::factory()->create(['name' => 'Dewi Lestari', 'role_id' => $mktRole->id]);

        $service = \App\Models\Service::firstOrCreate(['name' => 'Pajak & Akuntansi']);
        $sender = \App\Models\Sender::firstOrCreate(['name' => 'Instagram']);
        $group = \App\Models\Group::firstOrCreate(['name' => 'Group B']);
        $status = \App\Models\ProspectStatus::firstOrCreate(['slug' => 'open'], ['name' => 'On Process', 'color' => '#3B82F6']);

        \App\Models\Prospect::create([
            'client_phone' => '085711223344',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'status_id' => $status->id,
            'marketing_user_id' => $mktUser->id,
            'entry_date' => now()->toDateString(),
            'entry_time' => '11:00:00',
            'created_by' => $csUser->id,
        ]);

        $res = $this->actingAs($csUser)->getJson(route('prospects.check-phone', ['phone' => '085711223344']));
        $res->assertOk();
        $res->assertJson([
            'exists' => true,
            'marketing' => 'Dewi Lestari',
        ]);
        $this->assertStringContainsString('Dewi Lestari', $res->json('message'));

        // Cek nomor yang belum ada
        $resNonExisting = $this->actingAs($csUser)->getJson(route('prospects.check-phone', ['phone' => '089999999999']));
        $resNonExisting->assertOk();
        $resNonExisting->assertJson(['exists' => false]);
    }

    public function test_marketing_dashboard_renders_without_cancel_card(): void
    {
        $mktRole = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);
        $mktUser = User::factory()->create(['role_id' => $mktRole->id]);

        $response = $this->actingAs($mktUser)->get(route('dashboard'));

        $response->assertOk();
        $response->assertDontSee('Total Prospek Cancel');
        $response->assertSee('Prospek Hari Ini');
        $response->assertSee('Prospek Bulan Ini');
        $response->assertSee('Total Prospek Open');
        $response->assertSee('Total Prospek Closed');
        $response->assertSee('To Do Hari Ini');
        $response->assertSee('Tugas Selesai');
        $response->assertSee('/7');
        $response->assertSee('Link Medsos');
        $response->assertSee('/12');
        $response->assertSee('Upload PDF');
        $response->assertSee('/5');
    }

    public function test_prospects_index_separates_iklan_and_non_iklan_sections_and_modal_payload(): void
    {
        $managerRole = Role::firstOrCreate(['slug' => 'manager_marketing'], ['name' => 'Manager Marketing']);
        $manager = User::factory()->create(['role_id' => $managerRole->id]);

        $service = \App\Models\Service::firstOrCreate(['name' => 'Legalitas PT']);
        $sender = \App\Models\Sender::firstOrCreate(['name' => 'Website']);
        $nonIklanGroup = \App\Models\Group::firstOrCreate(['name' => 'Organik SEO']);
        $anotherNonIklanGroup = \App\Models\Group::firstOrCreate(['name' => 'Referral']);
        $iklanGroup = \App\Models\Group::firstOrCreate(['name' => 'Iklan Meta']);
        $openStatus = \App\Models\ProspectStatus::firstOrCreate(['slug' => 'open'], ['name' => 'On Process', 'color' => '#3B82F6']);

        // 1. Prospek non-iklan dengan grup Organik
        $p1 = \App\Models\Prospect::create([
            'client_phone' => '081111111111',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $nonIklanGroup->id,
            'status_id' => $openStatus->id,
            'marketing_user_id' => $manager->id,
            'entry_date' => now()->toDateString(),
            'entry_time' => '08:00:00',
            'created_by' => $manager->id,
        ]);

        // 2. Prospek non-iklan dengan grup Referral
        $p2 = \App\Models\Prospect::create([
            'client_phone' => '082222222222',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $anotherNonIklanGroup->id,
            'status_id' => $openStatus->id,
            'marketing_user_id' => $manager->id,
            'entry_date' => now()->toDateString(),
            'entry_time' => '09:00:00',
            'created_by' => $manager->id,
        ]);

        // 3. Prospek khusus grup Iklan
        $p3 = \App\Models\Prospect::create([
            'client_phone' => '083333333333',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $iklanGroup->id,
            'status_id' => $openStatus->id,
            'marketing_user_id' => $manager->id,
            'entry_date' => now()->toDateString(),
            'entry_time' => '10:00:00',
            'created_by' => $manager->id,
        ]);

        $response = $this->actingAs($manager)->get(route('prospects.index'));
        $response->assertOk();

        // Cek label bagian Non-Iklan dan Iklan
        $response->assertSee('Prospek Reguler (Non-Iklan)');
        $response->assertSee('Data Prospek Reguler (Non-Iklan)');
        $response->assertSee('Total Non-Iklan');

        $response->assertSee('Prospek Khusus Grup Iklan');
        $response->assertSee('Data Prospek Khusus Grup Iklan');
        $response->assertSee('Total Prospek Iklan');

        // Cek data nomor telepon tampil di halaman
        $response->assertSee('081111111111');
        $response->assertSee('082222222222');
        $response->assertSee('083333333333');

        // Cek parameter export excel
        $response->assertSee('group_type=non_iklan');
        $response->assertSee('group_type=iklan');

        // Cek export filter group_type=iklan
        $exportIklanRes = $this->actingAs($manager)->get(route('prospects.export', ['group_type' => 'iklan']));
        $exportIklanRes->assertOk();

        // Cek export filter group_type=non_iklan
        $exportNonIklanRes = $this->actingAs($manager)->get(route('prospects.export', ['group_type' => 'non_iklan']));
        $exportNonIklanRes->assertOk();
    }

    public function test_rapid_double_submission_on_store_is_handled_gracefully_without_duplicate_error(): void
    {
        $csRole = Role::firstOrCreate(['slug' => 'cs'], ['name' => 'Customer Service']);
        $mktRole = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);

        $csUser = User::factory()->create(['role_id' => $csRole->id]);
        $mktUser = User::factory()->create(['name' => 'Aditya Pratama', 'role_id' => $mktRole->id]);

        $service = \App\Models\Service::firstOrCreate(['name' => 'Pendirian CV']);
        $sender = \App\Models\Sender::firstOrCreate(['name' => 'WhatsApp']);
        $group = \App\Models\Group::firstOrCreate(['name' => 'Group Fast']);

        $payload = [
            'client_phone' => '087788990011',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'marketing_user_id' => $mktUser->id,
            'entry_date' => now()->toDateString(),
            'entry_time' => '14:00',
        ];

        // First submit (Request 1)
        $res1 = $this->actingAs($csUser)->post(route('prospects.store'), $payload);
        $res1->assertRedirect(route('prospects.index'));
        $res1->assertSessionHas('status', 'Prospek berhasil dibuat.');

        // Second submit immediately (Request 2 - simulated rapid double-click)
        $res2 = $this->actingAs($csUser)->post(route('prospects.store'), $payload);
        $res2->assertRedirect(route('prospects.index'));
        $res2->assertSessionHasNoErrors();
        $res2->assertSessionHas('status', 'Prospek berhasil dibuat.');

        // Pastikan record di database HANYA ada 1
        $count = \App\Models\Prospect::where('client_phone', '087788990011')->count();
        $this->assertEquals(1, $count);
    }

    public function test_cs_sees_edit_data_prospek_form_with_baseline_fields_and_no_status_selector(): void
    {
        $csRole = Role::firstOrCreate(['slug' => 'cs'], ['name' => 'Customer Service']);
        $mktRole = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);

        $csUser = User::factory()->create(['role_id' => $csRole->id]);
        $mktUser = User::factory()->create(['name' => 'Marketing Satu', 'role_id' => $mktRole->id]);

        $service = \App\Models\Service::firstOrCreate(['name' => 'Perizinan Usaha']);
        $sender = \App\Models\Sender::firstOrCreate(['name' => 'WhatsApp CS']);
        $group = \App\Models\Group::firstOrCreate(['name' => 'Inbound']);
        $openStatus = \App\Models\ProspectStatus::firstOrCreate(['slug' => 'open'], ['name' => 'On Process', 'color' => '#3B82F6']);

        $prospect = \App\Models\Prospect::create([
            'client_phone' => '081234112233',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'status_id' => $openStatus->id,
            'marketing_user_id' => $mktUser->id,
            'entry_date' => now()->toDateString(),
            'entry_time' => '09:30:00',
            'created_by' => $csUser->id,
        ]);

        $response = $this->actingAs($csUser)->get(route('prospects.edit', $prospect));
        $response->assertOk();

        // Header CS: Edit Data Prospek
        $response->assertSee('Edit Data Prospek');
        $response->assertSee('Informasi Klien');
        $response->assertSee('Waktu Masuk Prospek');
        $response->assertSee('Pengirim Prospek');
        $response->assertSee('Marketing Penanggung Jawab');
        $response->assertSee('Simpan Perubahan');

        // CS TIDAK melihat selector status prospek / closing / cancel / keterangan prospek
        $response->assertDontSee('Detail Transaksi Closing');
        $response->assertDontSee('Perhatian: Prospek Akan Dihapus');
        $response->assertDontSee('Keterangan Prospek');
    }

    public function test_cs_can_update_baseline_prospect_data_and_redirects_to_index(): void
    {
        $csRole = Role::firstOrCreate(['slug' => 'cs'], ['name' => 'Customer Service']);
        $mktRole = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);

        $csUser = User::factory()->create(['role_id' => $csRole->id]);
        $mktUser1 = User::factory()->create(['name' => 'Marketing A', 'role_id' => $mktRole->id]);
        $mktUser2 = User::factory()->create(['name' => 'Marketing B', 'role_id' => $mktRole->id]);

        $service1 = \App\Models\Service::firstOrCreate(['name' => 'Layanan Lama']);
        $service2 = \App\Models\Service::firstOrCreate(['name' => 'Layanan Baru']);
        $sender = \App\Models\Sender::firstOrCreate(['name' => 'WhatsApp']);
        $group = \App\Models\Group::firstOrCreate(['name' => 'Grup A']);
        $openStatus = \App\Models\ProspectStatus::firstOrCreate(['slug' => 'open'], ['name' => 'On Process', 'color' => '#3B82F6']);

        $prospect = \App\Models\Prospect::create([
            'client_phone' => '081299887766',
            'service_id' => $service1->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'status_id' => $openStatus->id,
            'marketing_user_id' => $mktUser1->id,
            'entry_date' => now()->toDateString(),
            'entry_time' => '10:00:00',
            'created_by' => $csUser->id,
        ]);

        $updateResponse = $this->actingAs($csUser)->put(route('prospects.update', $prospect), [
            'client_phone' => '081299887766',
            'service_id' => $service2->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'source_id' => '',
            'marketing_user_id' => $mktUser2->id,
            'entry_date' => now()->toDateString(),
            'entry_time' => '11:15',
            'note' => 'Klien minta ganti marketing ke B.',
        ]);

        $updateResponse->assertRedirect(route('prospects.index'));
        $updateResponse->assertSessionHas('status', 'Data prospek berhasil diperbarui.');

        $prospect->refresh();
        $this->assertEquals($service2->id, $prospect->service_id);
        $this->assertEquals($mktUser2->id, $prospect->marketing_user_id);
        $this->assertEquals('11:15', substr($prospect->entry_time, 0, 5));
        $this->assertEquals('Klien minta ganti marketing ke B.', $prospect->note);
    }

    public function test_marketing_sees_update_status_and_closing_fields_without_baseline_fields(): void
    {
        $mktRole = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);
        $mktUser = User::factory()->create(['role_id' => $mktRole->id]);

        $service = \App\Models\Service::firstOrCreate(['name' => 'PT']);
        $sender = \App\Models\Sender::firstOrCreate(['name' => 'Website']);
        $group = \App\Models\Group::firstOrCreate(['name' => 'Group A']);
        $openStatus = \App\Models\ProspectStatus::firstOrCreate(['slug' => 'open'], ['name' => 'On Process', 'color' => '#3B82F6']);

        $prospect = \App\Models\Prospect::create([
            'client_phone' => '081233445566',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'status_id' => $openStatus->id,
            'marketing_user_id' => $mktUser->id,
            'entry_date' => now()->toDateString(),
            'entry_time' => '09:00:00',
            'created_by' => $mktUser->id,
        ]);

        $response = $this->actingAs($mktUser)->get(route('prospects.edit', $prospect));
        $response->assertOk();

        // Marketing view: Update Prospek & Status selector & Keterangan Prospek
        $response->assertSee('Update Prospek');
        $response->assertSee('Status Prospek');
        $response->assertSee('Detail Transaksi Closing');
        $response->assertSee('Keterangan Prospek');
        $response->assertSee('Simpan Update');

        // Marketing TIDAK melihat section edit data klien / assignment
        $response->assertDontSee('Informasi Klien');
        $response->assertDontSee('Waktu Masuk Prospek');
        $response->assertDontSee('Assignment & Sumber');
    }

    public function test_prospects_index_date_picker_filter_and_uppercase_status_options(): void
    {
        $managerRole = Role::firstOrCreate(['slug' => 'manager_marketing'], ['name' => 'Manager Marketing']);
        $user = User::factory()->create(['role_id' => $managerRole->id]);

        $service = \App\Models\Service::firstOrCreate(['name' => 'PT']);
        $sender = \App\Models\Sender::firstOrCreate(['name' => 'Website']);
        $group = \App\Models\Group::firstOrCreate(['name' => 'Group A']);
        $openStatus = \App\Models\ProspectStatus::firstOrCreate(['slug' => 'open'], ['name' => 'Open', 'color' => '#3B82F6']);
        $closingStatus = \App\Models\ProspectStatus::firstOrCreate(['slug' => 'closing'], ['name' => 'Closing', 'color' => '#10B981']);
        $cancelStatus = \App\Models\ProspectStatus::firstOrCreate(['slug' => 'cancel'], ['name' => 'Cancel', 'color' => '#EF4444']);

        // Buat prospek dengan tanggal berbeda
        $p1 = \App\Models\Prospect::create([
            'client_phone' => '081200000001',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'status_id' => $openStatus->id,
            'marketing_user_id' => $user->id,
            'entry_date' => '2026-09-01',
            'entry_time' => '08:00:00',
            'created_by' => $user->id,
        ]);

        $p2 = \App\Models\Prospect::create([
            'client_phone' => '081200000002',
            'service_id' => $service->id,
            'sender_id' => $sender->id,
            'group_id' => $group->id,
            'status_id' => $closingStatus->id,
            'marketing_user_id' => $user->id,
            'entry_date' => '2026-09-15',
            'entry_time' => '10:00:00',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('prospects.index'));
        $response->assertOk();

        // Verifikasi input date picker Tanggal Awal & Tanggal Akhir hadir
        $response->assertSee('Tanggal Awal');
        $response->assertSee('name="start_date"', false);
        $response->assertSee('type="date"', false);
        $response->assertSee('Tanggal Akhir');
        $response->assertSee('name="end_date"', false);

        // Verifikasi dropdown bulan & tahun sudah digantikan
        $response->assertDontSee('name="month"', false);
        $response->assertDontSee('name="year"', false);
        $response->assertDontSee('Semua Bulan');
        $response->assertDontSee('Semua Tahun');

        // Verifikasi status dropdown memuat OPEN dan CLOSING uppercase, dan TIDAK memuat Cancel
        $response->assertSee('>OPEN</option>', false);
        $response->assertSee('>CLOSING</option>', false);
        $response->assertDontSee('>CANCEL</option>', false);
        $response->assertDontSee('>Cancel</option>', false);

        // Verifikasi badge pada tabel
        $response->assertSee('CLOSING');

        // Test filtering by start_date dan end_date (keduanya harus diisi)
        $filterRes = $this->actingAs($user)->get(route('prospects.index', [
            'start_date' => '2026-09-10',
            'end_date' => '2026-09-20',
        ]));
        $filterRes->assertOk();
        $filterRes->assertSee('081200000002');
        $filterRes->assertDontSee('081200000001');

        // Jika hanya salah satu tanggal diisi, filter tanggal tidak diterapkan
        $singleDateRes = $this->actingAs($user)->get(route('prospects.index', [
            'start_date' => '2026-09-10',
        ]));
        $singleDateRes->assertOk();
        $singleDateRes->assertSee('081200000001');
        $singleDateRes->assertSee('081200000002');

        // Test export with start_date & end_date
        $exportRes = $this->actingAs($user)->get(route('prospects.export', [
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-30',
            'group_type' => 'non_iklan',
        ]));
        $exportRes->assertOk();
    }
}
