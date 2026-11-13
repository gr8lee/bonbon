@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-10 px-6 space-y-10">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-black uppercase text-gray-900">Active Devices & Login Audit</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold uppercase text-cyan-600 hover:underline">&larr; Back to Dashboard</a>
    </div>

    @if (session('status'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm font-bold">
            {{ session('status') }}
        </div>
    @endif

    <!-- Currently Active Sessions -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-black uppercase text-gray-900 mb-4">Currently Logged-In Users</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="border-b uppercase text-gray-500">
                    <tr>
                        <th class="py-3">User</th>
                        <th class="py-3">Device / Browser</th>
                        <th class="py-3">IP Address</th>
                        <th class="py-3">Last Active</th>
                        <th class="py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($activeSessions as $session)
                        <tr>
                            <td class="py-3">
                                <p class="font-bold text-gray-900">{{ $session->name }}</p>
                                <p class="text-gray-500">{{ $session->email }}</p>
                            </td>
                            <td class="py-3 max-w-xs truncate text-gray-600">{{ $session->user_agent }}</td>
                            <td class="py-3 font-mono text-gray-700">{{ $session->ip_address }}</td>
                            <td class="py-3 text-gray-600">{{ $session->last_active }}</td>
                            <td class="py-3 text-right">
                                @if ($session->id !== session()->getId())
                                    <form method="POST" action="{{ route('admin.devices.revoke', $session->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white px-3 py-1 rounded-full font-bold uppercase transition">
                                            Force Logout
                                        </button>
                                    </form>
                                @else
                                    <span class="bg-cyan-100 text-cyan-700 px-3 py-1 rounded-full font-bold uppercase">Current</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-gray-400">No active authenticated sessions.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Permanent Login History -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-black uppercase text-gray-900 mb-4">Recent Login History</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="border-b uppercase text-gray-500">
                    <tr>
                        <th class="py-3">User</th>
                        <th class="py-3">Device / Browser</th>
                        <th class="py-3">IP Address</th>
                        <th class="py-3">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($loginLogs as $log)
                        <tr>
                            <td class="py-3 font-bold text-gray-900">{{ $log->name }} <span class="font-normal text-gray-500">({{ $log->email }})</span></td>
                            <td class="py-3 max-w-xs truncate text-gray-600">{{ $log->user_agent }}</td>
                            <td class="py-3 font-mono text-gray-700">{{ $log->ip_address }}</td>
                            <td class="py-3 text-gray-600">{{ \Carbon\Carbon::parse($log->logged_in_at)->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-gray-400">No login events recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection