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
}
