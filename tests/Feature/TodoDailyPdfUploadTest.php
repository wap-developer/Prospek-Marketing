<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Todo;
use App\Models\TodoPdf;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TodoDailyPdfUploadTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\TodoLockSetting::instance()->unlock();
    }

    public function test_marketing_user_can_upload_pdf_for_task_2(): void
    {
        Storage::fake('public');

        $role = Role::firstOrCreate(
            ['slug' => 'marketing'],
            ['name' => 'Marketing']
        );

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $file = UploadedFile::fake()->create('laporan_broadcast.pdf', 200, 'application/pdf');

        $response = $this->actingAs($user)->post(route('todos.daily.store'), [
            'date' => '2026-09-11',
            'task' => 2,
            'pdfs' => [
                2 => [$file],
            ],
        ]);

        $response->assertRedirect(route('todos.daily', [
            'date' => '2026-09-11',
            'view' => 1,
            'tab' => 2,
        ]));

        $todo = Todo::where('user_id', $user->id)->whereDate('date', '2026-09-11')->first();
        $this->assertNotNull($todo);

        $this->assertDatabaseHas('todo_pdfs', [
            'todo_id' => $todo->id,
            'task' => 2,
            'original_name' => 'laporan_broadcast.pdf',
        ]);

        $pdf = TodoPdf::where('todo_id', $todo->id)->where('task', 2)->first();
        Storage::disk('public')->assertExists($pdf->file_path);
    }

    public function test_marketing_user_uploads_single_pdf_for_task_2_replacing_previous(): void
    {
        Storage::fake('public');

        $role = Role::firstOrCreate(
            ['slug' => 'marketing'],
            ['name' => 'Marketing']
        );

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $file1 = UploadedFile::fake()->create('file1.pdf', 100, 'application/pdf');
        $file2 = UploadedFile::fake()->create('file2.pdf', 150, 'application/pdf');

        $this->actingAs($user)->post(route('todos.daily.store'), [
            'date' => '2026-09-11',
            'task' => 2,
            'pdfs' => [
                2 => [$file1],
            ],
        ]);

        $todo = Todo::where('user_id', $user->id)->whereDate('date', '2026-09-11')->first();
        $this->assertEquals(1, $todo->pdfs()->where('task', 2)->count());
        $this->assertEquals('file1.pdf', $todo->pdfs()->where('task', 2)->first()->original_name);

        // Uploading file2 replaces file1 (maks 1 file per tugas)
        $this->actingAs($user)->post(route('todos.daily.store'), [
            'date' => '2026-09-11',
            'task' => 2,
            'pdfs' => [
                2 => [$file2],
            ],
        ]);

        $todo->refresh();
        $this->assertEquals(1, $todo->pdfs()->where('task', 2)->count());
        $this->assertEquals('file2.pdf', $todo->pdfs()->where('task', 2)->first()->original_name);
    }

    public function test_todo_daily_displays_other_video_platform_label(): void
    {
        $role = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get(route('todos.daily', [
            'date' => '2026-09-03',
            'view' => 1,
        ]));

        $response->assertOk();
        $response->assertSee('Other Video');
        $response->assertSee('OV');
        $response->assertDontSee('Snack Video');
        $response->assertDontSee('https://other video.com/...');
    }

    public function test_marketing_user_can_upload_image_for_tasks_2_through_6(): void
    {
        Storage::fake('public');

        $role = Role::firstOrCreate(
            ['slug' => 'marketing'],
            ['name' => 'Marketing']
        );

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $imageJpg = UploadedFile::fake()->image('bukti_iklan.jpg', 600, 400);
        $imagePng = UploadedFile::fake()->image('bukti_dm.png', 500, 500);

        // Upload JPG untuk tugas 3 (Mengiklankan Akun Instagram)
        $response1 = $this->actingAs($user)->post(route('todos.daily.store'), [
            'date' => '2026-09-12',
            'task' => 3,
            'pdfs' => [
                3 => [$imageJpg],
            ],
        ]);

        $response1->assertRedirect(route('todos.daily', [
            'date' => '2026-09-12',
            'view' => 1,
            'tab' => 3,
        ]));

        $todo = Todo::where('user_id', $user->id)->whereDate('date', '2026-09-12')->first();
        $this->assertNotNull($todo);

        $this->assertDatabaseHas('todo_pdfs', [
            'todo_id' => $todo->id,
            'task' => 3,
            'original_name' => 'bukti_iklan.jpg',
        ]);

        // Upload PNG untuk tugas 4 (DM Brosur)
        $response2 = $this->actingAs($user)->post(route('todos.daily.store'), [
            'date' => '2026-09-12',
            'task' => 4,
            'pdfs' => [
                4 => [$imagePng],
            ],
        ]);

        $response2->assertRedirect(route('todos.daily', [
            'date' => '2026-09-12',
            'view' => 1,
            'tab' => 4,
        ]));

        $this->assertDatabaseHas('todo_pdfs', [
            'todo_id' => $todo->id,
            'task' => 4,
            'original_name' => 'bukti_dm.png',
        ]);

        // Verifikasi di view tampil badge JPG dan PNG
        $viewResponse = $this->actingAs($user)->get(route('todos.daily', [
            'date' => '2026-09-12',
            'view' => 1,
        ]));

        $viewResponse->assertOk();
        $viewResponse->assertSee('accept=".pdf,image/*,.jpg,.jpeg,.png,.webp"', false);
        $viewResponse->assertSee('bukti_iklan.jpg');
        $viewResponse->assertSee('bukti_dm.png');
        $viewResponse->assertSee('is-img');
        $viewResponse->assertSee('JPG');
        $viewResponse->assertSee('PNG');
    }

    public function test_disallowed_file_types_are_rejected(): void
    {
        Storage::fake('public');

        $role = Role::firstOrCreate(
            ['slug' => 'marketing'],
            ['name' => 'Marketing']
        );

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $invalidFile = UploadedFile::fake()->create('malicious.exe', 100, 'application/x-msdownload');

        $response = $this->actingAs($user)->post(route('todos.daily.store'), [
            'date' => '2026-09-12',
            'task' => 2,
            'pdfs' => [
                2 => [$invalidFile],
            ],
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_files_up_to_100mb_are_allowed_and_over_100mb_are_rejected(): void
    {
        Storage::fake('public');

        $role = Role::firstOrCreate(['slug' => 'marketing'], ['name' => 'Marketing']);
        $user = User::factory()->create(['role_id' => $role->id]);

        // File 15MB (15360 KB) - sebelumnya ditolak di 10MB, sekarang harus lolos
        $validLargeFile = UploadedFile::fake()->create('large_proof.pdf', 15360, 'application/pdf');

        $response = $this->actingAs($user)->post(route('todos.daily.store'), [
            'date' => '2026-10-01',
            'task' => 2,
            'pdfs' => [
                2 => [$validLargeFile],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // File 105MB (107520 KB) - harus ditolak karena > 100MB
        $overLimitFile = UploadedFile::fake()->create('overlimit.pdf', 107520, 'application/pdf');

        $errorResponse = $this->actingAs($user)->post(route('todos.daily.store'), [
            'date' => '2026-10-01',
            'task' => 2,
            'pdfs' => [
                2 => [$overLimitFile],
            ],
        ]);

        $errorResponse->assertSessionHasErrors('pdfs.2.0');
    }
}
