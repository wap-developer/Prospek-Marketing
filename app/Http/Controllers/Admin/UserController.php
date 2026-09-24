<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->role?->slug === 'super_admin', 403);

        $query = User::with('role');

        if ($request->filled('q')) {
            $term = '%' . $request->string('q') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('username', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->integer('role_id'));
        }

        $users = $query->orderBy('id', 'asc')->paginate(15)->withQueryString();
        $roles = Role::orderBy('id')->get();
        $creatableRoles = Role::where('slug', '!=', 'super_admin')->orderBy('id')->get();

        $roleCounts = User::join('roles', 'users.role_id', '=', 'roles.id')
            ->selectRaw('roles.slug, count(users.id) as total')
            ->groupBy('roles.slug')
            ->pluck('total', 'slug');

        $stats = [
            'total' => User::count(),
            'marketing' => $roleCounts['marketing'] ?? 0,
            'manager_marketing' => $roleCounts['manager_marketing'] ?? 0,
            'cs' => $roleCounts['cs'] ?? 0,
            'super_admin' => $roleCounts['super_admin'] ?? 0,
        ];

        return view('admin.users.index', compact('users', 'roles', 'creatableRoles', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->role?->slug === 'super_admin', 403);

        $superAdminRole = Role::where('slug', 'super_admin')->first();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'max:64', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role_id' => [
                'required',
                'exists:roles,id',
                $superAdminRole ? Rule::notIn([$superAdminRole->id]) : 'nullable',
            ],
        ], [
            'role_id.not_in' => 'Role Super Admin tidak dapat ditambahkan.',
        ]);

        User::create($data);

        return redirect()->route('admin.users.index')->with('status', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->role?->slug === 'super_admin', 403);

        $superAdminRole = Role::where('slug', 'super_admin')->first();

        // Jika user super_admin tidak mengirim role_id (karena field dikunci di form), pertahankan role_id aslinya
        if ($user->role?->slug === 'super_admin' && ! $request->filled('role_id')) {
            $request->merge(['role_id' => $user->role_id]);
        }

        $roleRules = ['required', 'exists:roles,id'];
        if ($user->role?->slug !== 'super_admin' && $superAdminRole) {
            $roleRules[] = Rule::notIn([$superAdminRole->id]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'max:64', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role_id' => $roleRules,
        ], [
            'role_id.not_in' => 'Role Super Admin tidak dapat dipilih.',
        ]);

        // Jika user adalah super_admin, role tidak boleh diubah ke selain super_admin
        if ($user->role?->slug === 'super_admin' && $superAdminRole && (int) $data['role_id'] !== (int) $superAdminRole->id) {
            return back()->withErrors(['role_id' => 'Role Super Admin tidak dapat diubah.']);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('status', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->role?->slug === 'super_admin', 403);
        abort_if($user->id === $request->user()->id, 403, 'Tidak bisa hapus akun sendiri.');

        if ($user->role?->slug === 'super_admin') {
            return back()->withErrors(['name' => 'Akun Super Admin tidak dapat dihapus.']);
        }

        try {
            $user->delete();
        } catch (\Throwable) {
            return back()->withErrors(['name' => 'User memiliki data terkait, tidak dapat dihapus.']);
        }

        return redirect()->route('admin.users.index')->with('status', 'User berhasil dihapus.');
    }
}
