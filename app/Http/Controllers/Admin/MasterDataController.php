<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\ProspectSource;
use App\Models\Sender;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterDataController extends Controller
{
    private const RESOURCES = [
        'services' => [
            'label' => 'Layanan / Jasa',
            'model' => Service::class,
            'icon' => 'briefcase',
            'desc' => 'Daftar paket dan jenis layanan yang ditawarkan',
        ],
        'senders' => [
            'label' => 'Pengirim Prospek',
            'model' => Sender::class,
            'icon' => 'send',
            'desc' => 'Pihak yang mengirimkan prospek ke tim marketing',
        ],
        'groups' => [
            'label' => 'Group',
            'model' => Group::class,
            'icon' => 'users',
            'desc' => 'Kelompok atau grup kerja dalam tim marketing',
        ],
        'prospect_sources' => [
            'label' => 'Sumber Prospek',
            'model' => ProspectSource::class,
            'icon' => 'globe',
            'desc' => 'Kanal perolehan data prospek (Instagram, Web, dll)',
        ],
    ];

    public function index(Request $request): View
    {
        abort_unless($request->user()->role?->slug === 'super_admin', 403);

        $data = [
            'resources' => self::RESOURCES,
        ];
        foreach (self::RESOURCES as $key => $cfg) {
            $data[$key] = $cfg['model']::withCount('prospects')->orderBy('name')->get();
            $data["{$key}_label"] = $cfg['label'];
        }

        return view('admin.masters.index', $data);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        abort_unless($request->user()->role?->slug === 'super_admin', 403);
        $this->assertResource($resource);

        $data = $request->validate(['name' => ['required', 'string', 'max:120']]);
        self::RESOURCES[$resource]['model']::create($data);

        return redirect()->route('admin.masters.index')->with('status', 'Data ditambah.');
    }

    public function update(Request $request, string $resource, int $id): RedirectResponse
    {
        abort_unless($request->user()->role?->slug === 'super_admin', 403);
        $this->assertResource($resource);

        $data = $request->validate(['name' => ['required', 'string', 'max:120']]);
        self::RESOURCES[$resource]['model']::findOrFail($id)->update($data);

        return redirect()->route('admin.masters.index')->with('status', 'Data diperbarui.');
    }

    public function destroy(Request $request, string $resource, int $id): RedirectResponse
    {
        abort_unless($request->user()->role?->slug === 'super_admin', 403);
        $this->assertResource($resource);

        try {
            self::RESOURCES[$resource]['model']::findOrFail($id)->delete();
        } catch (\Throwable) {
            return back()->withErrors(['name' => 'Data dipakai prospek, tidak bisa dihapus.']);
        }

        return redirect()->route('admin.masters.index')->with('status', 'Data dihapus.');
    }

    private function assertResource(string $resource): void
    {
        abort_unless(array_key_exists($resource, self::RESOURCES), 404);
    }
}
