<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): Response
    {
        $roles = Role::query()
            ->withCount('users')
            ->with('permissions')
            ->orderBy('name')
            ->get()
            ->map(fn (Role $r) => [
                'id' => $r->id,
                'name' => $r->name,
                'users_count' => $r->users_count,
                'permission_names' => $r->permissions->pluck('name')->values()->all(),
                'is_system' => in_array($r->name, ['super-admin', 'admin', 'customer'], true),
            ]);

        $allPermissions = Permission::query()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles->values()->all(),
            'allPermissions' => $allPermissions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->name}\" berhasil dibuat.");
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', "Permissions untuk role \"{$role->name}\" berhasil diperbarui.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, ['super-admin', 'admin', 'customer'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', "Role \"{$role->name}\" tidak dapat dihapus.");
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->name}\" berhasil dihapus.");
    }
}
