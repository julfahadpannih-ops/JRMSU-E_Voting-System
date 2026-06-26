@extends('layouts.app')
@section('title', 'Admin Login – JRMSU SSG E-Voting')

@section('body')
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl border-t-4 border-secondary">
        <div class="text-center mb-8">
            <img src="{{ asset('images/OIP.jpg') }}" alt="JRMSU Logo"
                 class="mx-auto w-24 h-24 rounded-full object-cover border-2 border-secondary shadow-md mb-4">
            <h1 class="text-2xl font-bold text-primary">JRMSU E-Voting</h1>
            <p class="text-gray-500 text-sm mt-1">Admin Panel — Please sign in</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $errors->first() }}
            </div>
        @endif

        
    @if(session('status'))
        <div class="text-sm text-green-600 mb-3 p-3 bg-green-50 rounded-lg">{{ session('status') }}</div>
    @endif
    @error('_throttle')
        <div class="text-sm text-red-600 mb-3 p-3 bg-red-50 rounded-lg border border-red-200">
            ⚠️ Too many login attempts. Please wait 1 minute before trying again.
        </div>
    @enderror
<form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="mb-5">
                <label for="username" class="block mb-2 text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" name="username" required value="{{ old('username') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-secondary focus:border-secondary block w-full p-2.5 outline-none transition-all"
                    placeholder="Denver_admin">
            </div>

            <div class="mb-6">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-secondary focus:border-secondary block w-full p-2.5 outline-none transition-all"
                    placeholder="••••••••">
            </div>

            <button type="submit"
                class="w-full text-white bg-primary hover:bg-secondary focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors duration-300">
                Sign In
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-gray-400">
            <p>&copy; {{ date('Y') }} JRMSU Siocon SSG. All rights reserved.</p>
        </div>
    </div>
</div>
@endsection
