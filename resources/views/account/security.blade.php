@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-6 space-y-8">
    <h1 class="text-2xl font-black uppercase text-gray-900">Account & Device Security</h1>

    @if (session('status'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm font-bold">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-bold">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Active Logged-In Devices -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-black uppercase text-gray-900 mb-1">Logged-In Devices</h2>
        <p class="text-xs text-gray-500 mb-5">Review active browser sessions on your account.</p>

        <div class="space-y-3 mb-6">
            @foreach ($sessions as $session)
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <p class="text-xs font-bold text-gray-900 line-clamp-1">{{ $session->user_agent }}</p>
                        <p class="text-[11px] text-gray-500">
                            IP: {{ $session->ip_address }} · Last active {{ $session->last_active }}
                        </p>
                    </div>
                    @if ($session->is_current_device)
                        <span class="bg-cyan-100 text-cyan-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase">This Device</span>
                    @endif
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ route('account.logout-others') }}" class="flex flex-wrap gap-3 items-center pt-4 border-t">
            @csrf
            <input type="password" name="password" placeholder="Confirm password to sign out others" required class="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-full text-xs font-bold uppercase">
                Log Out All Other Devices
            </button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-black uppercase text-gray-900 mb-4">Change Password</h2>
        <form method="POST" action="{{ route('account.password') }}" class="space-y-3">
            @csrf
            @method('PUT')
            <input type="password" name="current_password" placeholder="Current Password" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs">
            <input type="password" name="password" placeholder="New Password" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs">
            <input type="password" name="password_confirmation" placeholder="Confirm New Password" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs">
            <button type="submit" class="bg-gray-900 hover:bg-black text-white px-6 py-2.5 rounded-full text-xs font-bold uppercase">
                Update Password
            </button>
        </form>
    </div>
</div>
@endsection