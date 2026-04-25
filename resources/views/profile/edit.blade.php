@extends('layouts.account')

@section('account-content')
<div class="space-y-6">
    <div>
        <h1 class="display-font text-4xl text-base-content font-normal lg:text-5xl">{{ __('Profile') }}</h1>
        <p class="mt-2 text-sm text-base-content/55">{{ __('Manage your account details and security settings.') }}</p>
    </div>

    <div class="rounded-[20px] border border-base-300 bg-base-100 p-5 sm:p-6">
        @include('profile.partials.update-profile-information-form')
    </div>

    <div class="rounded-[20px] border border-base-300 bg-base-100 p-5 sm:p-6">
        @include('profile.partials.update-password-form')
    </div>

    <div class="rounded-[20px] border border-error/30 bg-base-100 p-5 sm:p-6">
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection
