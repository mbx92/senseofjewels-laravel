@extends('layouts.admin')

@section('content')
<div class="space-y-8">

    {{-- Heading + filter bar --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div class="space-y-1">
            <p class="text-[10px] uppercase tracking-[0.25em] text-primary">System</p>
            <h1 class="display-font text-4xl text-base-content font-normal">Users</h1>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search name / email..."
                   class="border-b border-base-content/20 bg-transparent py-1.5 text-xs placeholder:text-base-content/40 focus:outline-none focus:border-primary transition-colors w-48">
            <select name="role" class="border-b border-base-content/20 bg-transparent py-1.5 text-xs focus:outline-none focus:border-primary transition-colors">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="text-[10px] uppercase tracking-widest border border-base-content/30 px-3 py-1.5 hover:bg-base-content hover:text-base-100 transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('admin.users.index') }}" class="text-[10px] uppercase tracking-widest text-base-content/40 hover:text-base-content transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-base-100 border border-base-300">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-base-200">
                    <th class="text-left px-6 py-3.5 text-[10px] uppercase tracking-widest text-base-content/40 font-normal">Name</th>
                    <th class="text-left px-6 py-3.5 text-[10px] uppercase tracking-widest text-base-content/40 font-normal">Email</th>
                    <th class="text-left px-6 py-3.5 text-[10px] uppercase tracking-widest text-base-content/40 font-normal">Role</th>
                    <th class="text-left px-6 py-3.5 text-[10px] uppercase tracking-widest text-base-content/40 font-normal">Joined</th>
                    <th class="px-6 py-3.5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-b border-base-200 last:border-0 hover:bg-base-200/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xs font-semibold shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="text-sm text-base-content">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-xs text-base-content/60">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @foreach($user->roles as $role)
                            <span class="text-[9px] uppercase tracking-widest border border-base-content/20 px-2.5 py-1 text-base-content/60 mr-1">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 text-xs text-base-content/50">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="text-[10px] uppercase tracking-widest text-primary hover:underline">
                                Edit
                            </a>
                            @unless($user->id === auth()->id())
                            <div x-data="{ open: false }">
                                <button @click="open = true"
                                        class="text-[10px] uppercase tracking-widest text-error hover:underline">
                                    Delete
                                </button>
                                <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                                    <div class="bg-base-100 w-full max-w-sm p-8 shadow-2xl">
                                        <h3 class="display-font text-2xl mb-3">Hapus User</h3>
                                        <p class="text-sm text-base-content/60 mb-6">User <span class="font-semibold text-base-content">{{ $user->name }}</span> akan dihapus permanen.</p>
                                        <div class="flex items-center gap-4">
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
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
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-sm text-base-content/40">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="flex justify-end">
        {{ $users->links() }}
    </div>
    @endif

</div>
@endsection
