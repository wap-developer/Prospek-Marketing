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
}
