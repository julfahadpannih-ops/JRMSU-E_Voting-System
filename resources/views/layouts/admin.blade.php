@extends('layouts.app')

@section('body')
<div class="flex min-h-screen">
    <x-admin-sidebar :active="$activeView ?? ''"/>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 px-4 md:px-8 py-4 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center space-x-3">
                <button id="open-sidebar" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                    <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>
                <div>
                    <h1 class="text-xl font-bold text-primary">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-xs text-gray-400 hidden sm:block">@yield('page-subtitle', '')</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="hidden sm:block text-sm text-gray-600">
                    Welcome, <strong>{{ Auth::guard('admin')->user()->name }}</strong>
                </span>
                <div class="h-9 w-9 bg-primary rounded-full flex items-center justify-center text-white text-sm font-bold">
                    {{ strtoupper(substr(Auth::guard('admin')->user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="mx-4 md:mx-8 mt-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg" id="flash-msg">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-4 md:mx-8 mt-4 p-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg" id="flash-msg">
            {{ session('error') }}
        </div>
        @endif

        <main class="flex-1 p-4 md:p-8 overflow-auto">
            @yield('content')
        </main>
    </div>
</div>

@push('scripts')
<script>
    // Sidebar mobile toggle
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    document.getElementById('open-sidebar')?.addEventListener('click', () => {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    });
    document.getElementById('close-sidebar')?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
    // Auto-hide flash
    setTimeout(() => document.getElementById('flash-msg')?.remove(), 4000);

    // Global CSRF helper for fetch()
    window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    window.apiFetch = async (url, options = {}) => {
        const defaults = {
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
        };
        const merged = { ...defaults, ...options, headers: { ...defaults.headers, ...options.headers } };
        const res = await fetch(url, merged);
        return res.json();
    };
</script>
@endpush
@endsection
