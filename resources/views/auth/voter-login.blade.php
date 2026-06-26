@extends('layouts.app')
@section('title', 'Voter Login – JRMSU SSG E-Voting')

@section('body')
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl border-t-4 border-secondary">
        <div class="text-center mb-8">
            <img src="{{ asset('images/SSG.jpg') }}" alt="SSG Logo"
                 class="mx-auto w-24 h-24 rounded-full object-cover border-2 border-secondary shadow-md mb-4">
            <h1 class="text-2xl font-bold text-primary">JRMSU SSG Election</h1>
            <p class="text-gray-500 text-sm mt-1">Siocon Campus — Voter Portal</p>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Login --}}
        
    @error('_throttle')
        <div class="text-sm text-red-600 mb-3 p-3 bg-red-50 rounded-lg border border-red-200">
            ⚠️ Too many login attempts. Please wait 1 minute before trying again.
        </div>
    @enderror
<form method="POST" action="{{ route('voter.login.post') }}" class="mb-6">
            @csrf
            <div class="mb-4">
                <label for="student_id" class="block mb-2 text-sm font-medium text-gray-700">Student ID</label>
                <input type="text" id="student_id" name="student_id" required value="{{ old('student_id') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-secondary focus:border-secondary block w-full p-2.5 outline-none transition-all"
                    placeholder="e.g. 2021-00001">
            </div>

            <div class="mb-4">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-700">
                    Password
                    <span class="text-xs text-gray-400 font-normal ml-1">(create one on first login)</span>
                </label>
                <input type="password" id="password" name="password" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-secondary focus:border-secondary block w-full p-2.5 outline-none transition-all"
                    placeholder="••••••••">
            </div>

            <button type="submit"
                class="w-full text-white bg-primary hover:bg-secondary font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors duration-300">
                Sign In / Set Password
            </button>
        </form>

        {{-- Register --}}
        <details class="border border-gray-200 rounded-lg">
            <summary class="cursor-pointer p-3 text-sm font-medium text-primary select-none">
                Not in the voter list? Register here
            </summary>
            <div class="p-4 border-t border-gray-200">
                <form method="POST" action="{{ route('voter.register') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="block mb-1 text-xs font-medium text-gray-600">Student ID</label>
                        <input type="text" name="student_id" required
                            class="bg-gray-50 border border-gray-300 text-xs rounded-lg block w-full p-2 outline-none focus:ring-secondary focus:border-secondary"
                            placeholder="2021-00001">
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-xs font-medium text-gray-600">Full Name</label>
                        <input type="text" name="name" required
                            class="bg-gray-50 border border-gray-300 text-xs rounded-lg block w-full p-2 outline-none focus:ring-secondary focus:border-secondary"
                            placeholder="Juan Dela Cruz">
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-xs font-medium text-gray-600">Course</label>
                        <input type="text" name="course" required
                            class="bg-gray-50 border border-gray-300 text-xs rounded-lg block w-full p-2 outline-none focus:ring-secondary focus:border-secondary"
                            placeholder="BSIS">
                    </div>
                    <button type="submit"
                        class="w-full text-white bg-secondary hover:bg-yellow-600 font-medium rounded-lg text-xs px-4 py-2 text-center transition-colors">
                        Submit Registration
                    </button>
                </form>
            </div>
        </details>

        <div class="mt-6 text-center text-xs text-gray-400">
            <p>&copy; {{ date('Y') }} JRMSU Siocon SSG. All rights reserved.</p>
        </div>
    </div>
</div>
@endsection
