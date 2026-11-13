@extends('layouts.app')

@section('content')
<div x-data="{ tab: 'login' }" class="max-w-md mx-auto my-14 bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
    <div class="flex border-b mb-6">
        <button type="button" @click="tab = 'login'" :class="tab === 'login' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-400'" class="flex-1 pb-3 font-black uppercase text-xs tracking-wider border-b-2 transition">
            Sign In
        </button>
        <button type="button" @click="tab = 'register'" :class="tab === 'register' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-400'" class="flex-1 pb-3 font-black uppercase text-xs tracking-wider border-b-2 transition">
            Create Account
        </button>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 text-red-700 text-xs font-bold p-3 rounded-xl mb-4">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Sign In Form -->
    <form x-show="tab === 'login'" method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Password</label>
            <input type="password" name="password" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3 rounded-full uppercase text-xs tracking-wider transition">
            Sign In
        </button>
    </form>

    <!-- Register Form -->
    <form x-show="tab === 'register'" style="display: none;" method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Full Name</label>
            <input type="text" name="name" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Email</label>
            <input type="email" name="email" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Password</label>
            <input type="password" name="password" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="w-full bg-gray-900 hover:bg-black text-white font-bold py-3 rounded-full uppercase text-xs tracking-wider transition">
            Create Account
        </button>
    </form>
</div>
@endsection