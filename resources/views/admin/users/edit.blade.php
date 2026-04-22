@extends('layouts.admin')

@section('content')
<div class="max-w-2xl space-y-8">

    {{-- Heading --}}
    <div class="space-y-1">
        <p class="text-[10px] uppercase tracking-[0.25em] text-primary">Users</p>
        <h1 class="display-font text-4xl text-base-content font-normal">Edit User</h1>
        <p class="text-sm text-base-content/50">{{ $user->email }}</p>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
        @csrf @method('PUT')

        {{-- Name --}}
        <div>
            <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Full Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors @error('name') border-error @enderror">
            @error('name')<p class="text-[11px] text-error mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors @error('email') border-error @enderror">
            @error('email')<p class="text-[11px] text-error mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Phone --}}
        <div>
            <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                   class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors">
        </div>

        {{-- New Password --}}
        <div>
            <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">New Password <span class="text-base-content/30">(leave blank to keep)</span></label>
            <input type="password" name="password"
                   class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors @error('password') border-error @enderror">
            @error('password')<p class="text-[11px] text-error mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Roles --}}
        <div>
            <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-3">Roles</label>
            <div class="space-y-2">
                @foreach($roles as $role)
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                           class="checkbox checkbox-sm checkbox-primary"
                           {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                    <span class="text-sm text-base-content/70">{{ $role->name }}</span>
                </label>
                @endforeach
            </div>
            @error('roles')<p class="text-[11px] text-error mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-6 pt-2">
            <button type="submit" class="text-[11px] uppercase tracking-widest bg-neutral text-neutral-content px-6 py-3 hover:bg-neutral/80 transition-colors">
                Save Changes
            </button>
            <a href="{{ route('admin.users.index') }}" class="text-[11px] uppercase tracking-widest text-base-content/50 hover:text-base-content transition-colors">
                Cancel
            </a>
        </div>
    </form>

</div>
@endsection
