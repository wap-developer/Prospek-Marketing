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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PaginationViewTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_users_pagination_renders_clean_svg_and_styles(): void
    {
        $role = Role::firstOrCreate(['slug' => 'super_admin'], ['name' => 'Super Admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);

        // Create enough users to trigger pagination (> 15)
        User::factory()->count(20)->create(['role_id' => $role->id]);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('table-pagination');
        $response->assertSee('hf-pagination');
        $response->assertSee('hf-pagination-info');
        $response->assertSee('hf-pagination-links');
        $response->assertSee('hf-page-item');
        $response->assertSee('Menampilkan');
        $response->assertSee('dari');
        $response->assertSee('polyline points="15 18 9 12 15 6"', false);
        $response->assertSee('polyline points="9 18 15 12 9 6"', false);
    }

    public function test_prospects_pagination_renders_clean_markup(): void
    {
        $role = Role::firstOrCreate(['slug' => 'manager_marketing'], ['name' => 'Manager Marketing']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $service = Service::firstOrCreate(['name' => 'PT Test']);
        $sender = Sender::firstOrCreate(['name' => 'Web Test']);
        $group = Group::firstOrCreate(['name' => 'Group Test']);
        $source = ProspectSource::firstOrCreate(['name' => 'Google Test']);
        $status = ProspectStatus::firstOrCreate(['slug' => 'open'], ['name' => 'On Process']);

        // Create > 20 prospects to trigger pagination
        for ($i = 0; $i < 25; $i++) {
            Prospect::create([
                'client_phone' => '0812999900' . str_pad($i, 2, '0', STR_PAD_LEFT),
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
        }

        $response = $this->actingAs($user)->get(route('prospects.index'));

        $response->assertOk();
        $response->assertSee('table-pagination');
        $response->assertSee('hf-pagination');
        $response->assertSee('hf-pagination-links');
        $response->assertSee('hf-page-item');
        $response->assertSee('polyline points="9 18 15 12 9 6"', false);
    }
}
