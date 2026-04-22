@extends('layouts.admin')

@section('content')
<div class="space-y-8">

    {{-- Page heading --}}
    <div class="flex items-end justify-between gap-4">
        <div class="space-y-1">
            <p class="text-[10px] uppercase tracking-[0.25em] text-primary">System</p>
            <h1 class="display-font text-4xl text-base-content font-normal">Roles & Permissions</h1>
        </div>

        {{-- Add Role Modal Trigger --}}
        <div x-data="{ open: false }">
            <button @click="open = true" class="text-[11px] uppercase tracking-widest border border-base-content/30 px-4 py-2 hover:bg-base-content hover:text-base-100 transition-colors">
                + Add Role
            </button>

            {{-- Modal --}}
            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                <div class="bg-base-100 w-full max-w-md p-8 shadow-2xl">
                    <h3 class="display-font text-2xl mb-6">New Role</h3>
                    <form method="POST" action="{{ route('admin.roles.store') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Role Name</label>
                                <input type="text" name="name" required
                                       class="w-full border-b border-base-content/20 bg-transparent py-2 text-sm focus:outline-none focus:border-primary transition-colors"
                                       placeholder="e.g. editor">
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-3">Permissions</label>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    @foreach($allPermissions as $permission)
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                               class="checkbox checkbox-xs checkbox-primary">
                                        <span class="text-xs text-base-content/70">{{ $permission->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mt-8">
                            <button type="submit" class="text-[11px] uppercase tracking-widest bg-neutral text-neutral-content px-6 py-2.5 hover:bg-neutral/80 transition-colors">
                                Create Role
                            </button>
                            <button type="button" @click="open = false" class="text-[11px] uppercase tracking-widest text-base-content/50 hover:text-base-content transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Roles table --}}
    <div class="bg-base-100 border border-base-300">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-base-200">
                    <th class="text-left px-6 py-3.5 text-[10px] uppercase tracking-widest text-base-content/40 font-normal">Role</th>
                    <th class="text-left px-6 py-3.5 text-[10px] uppercase tracking-widest text-base-content/40 font-normal">Permissions</th>
                    <th class="text-center px-6 py-3.5 text-[10px] uppercase tracking-widest text-base-content/40 font-normal">Users</th>
                    <th class="px-6 py-3.5"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr class="border-b border-base-200 last:border-0 hover:bg-base-200/30 transition-colors">
                    <td class="px-6 py-4">
                        <span class="text-xs font-mono text-base-content">{{ $role->name }}</span>
                        @if(in_array($role->name, ['super-admin', 'admin', 'customer']))
                            <span class="ml-2 text-[9px] uppercase tracking-widest border border-base-content/20 px-2 py-0.5 text-base-content/50">system</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="text-[10px] uppercase tracking-widest text-primary hover:underline">
                                {{ $role->permissions->count() }} permissions
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline w-3 h-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak @click.outside="open = false"
                                 class="absolute left-0 top-6 z-30 bg-base-100 border border-base-300 shadow-lg p-4 w-64">
                                <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                                    @csrf @method('PUT')
                                    <div class="space-y-2 max-h-48 overflow-y-auto mb-4">
                                        @foreach($allPermissions as $permission)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                   class="checkbox checkbox-xs checkbox-primary"
                                                   {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                            <span class="text-xs text-base-content/70">{{ $permission->name }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                    <button type="submit" class="text-[10px] uppercase tracking-widest bg-primary text-primary-content px-3 py-1.5 hover:bg-primary/80 transition-colors">
                                        Save
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center text-xs text-base-content/60">{{ $role->users_count }}</td>
                    <td class="px-6 py-4 text-right">
                        @unless(in_array($role->name, ['super-admin', 'admin', 'customer']))
                        <div x-data="{ open: false }">
                            <button @click="open = true"
                                    class="text-[10px] uppercase tracking-widest text-error hover:underline">
                                Delete
                            </button>
                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                                <div class="bg-base-100 w-full max-w-sm p-8 shadow-2xl">
                                    <h3 class="display-font text-2xl mb-3">Hapus Role</h3>
                                    <p class="text-sm text-base-content/60 mb-6">Role <span class="font-mono text-base-content">{{ $role->name }}</span> akan dihapus permanen.</p>
                                    <div class="flex items-center gap-4">
                                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-[11px] uppercase tracking-widest bg-error text-white px-5 py-2 hover:bg-error/80 transition-colors">
                                                Ya, Hapus
                                            </button>
                                        </form>
                                        <button @click="open = false" class="text-[11px] uppercase tracking-widest text-base-content/50 hover:text-base-content">
                                            Batal
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endunless
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
