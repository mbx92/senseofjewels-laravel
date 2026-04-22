<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with('roles')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($inner) use ($request) {
                    $inner->where('name', 'like', "%{$request->search}%")
                          ->orWhere('email', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('role'), function ($q) use ($request) {
                $q->whereHas('roles', fn ($r) => $r->where('name', $request->role));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $roles = Role::query()->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function edit(User $user): View
    {
        $roles = Role::query()->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone'    => ['nullable', 'string', 'max:30'],
            'roles'    => ['required', 'array'],
            'password' => ['nullable', Password::defaults()],
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles($validated['roles']);

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$name}\" berhasil dihapus.");
    }
}
