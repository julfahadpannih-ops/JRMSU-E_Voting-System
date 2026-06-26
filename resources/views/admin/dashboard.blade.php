@extends('layouts.admin')
@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'Welcome to the JRMSU Siocon SSG E-Voting System.')
@php $activeView = 'dashboard'; @endphp

@section('content')

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    @php
    $cards = [
        ['label'=>'Approved Voters',  'value'=>$stats['total_voters'],     'color'=>'border-blue-500',   'icon'=>'👥'],
        ['label'=>'Pending Approval', 'value'=>$stats['pending_voters'],   'color'=>'border-yellow-500', 'icon'=>'⏳'],
        ['label'=>'Voted',            'value'=>$stats['voted_count'],      'color'=>'border-green-500',  'icon'=>'✅'],
        ['label'=>'Candidates',       'value'=>$stats['total_candidates'], 'color'=>'border-purple-500', 'icon'=>'🏅'],
        ['label'=>'Positions',        'value'=>$stats['total_positions'],  'color'=>'border-indigo-500', 'icon'=>'📋'],
        ['label'=>'Total Votes Cast', 'value'=>$stats['total_votes'],      'color'=>'border-primary',    'icon'=>'🗳️'],
    ];
    @endphp
    @foreach($cards as $card)
    <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 {{ $card['color'] }} hover:-translate-y-1 transition-transform">
        <div class="text-2xl mb-1">{{ $card['icon'] }}</div>
        <div class="text-3xl font-extrabold text-primary">{{ $card['value'] }}</div>
        <div class="text-xs text-gray-500 mt-1">{{ $card['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Voter Turnout Bar --}}
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6 border border-gray-100">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-bold text-primary">🗳️ Voter Turnout</h2>
        <span class="text-2xl font-extrabold text-primary">{{ $stats['turnout_percent'] }}%</span>
    </div>
    <div class="w-full bg-gray-100 rounded-full h-5 overflow-hidden">
        <div class="h-5 rounded-full transition-all duration-700
            {{ $stats['turnout_percent'] >= 70 ? 'bg-green-500' : ($stats['turnout_percent'] >= 40 ? 'bg-yellow-400' : 'bg-red-400') }}"
            style="width: {{ $stats['turnout_percent'] }}%">
        </div>
    </div>
    <p class="text-xs text-gray-400 mt-2">{{ $stats['voted_count'] }} out of {{ $stats['total_voters'] }} approved voters have cast their ballot.</p>
</div>

{{-- Election window --}}
<div class="bg-white rounded-2xl shadow-sm p-6 mb-8 border border-gray-100">
    <h2 class="text-lg font-bold text-primary mb-4">⏰ Election Schedule</h2>
    <div class="grid sm:grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Start Time</label>
            <input type="datetime-local" id="start_time"
                value="{{ $start ? \Carbon\Carbon::parse($start)->format('Y-m-d\TH:i') : '' }}"
                class="border border-gray-300 rounded-lg text-sm p-2 w-full focus:outline-none focus:ring-2 focus:ring-secondary">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">End Time</label>
            <input type="datetime-local" id="end_time"
                value="{{ $end ? \Carbon\Carbon::parse($end)->format('Y-m-d\TH:i') : '' }}"
                class="border border-gray-300 rounded-lg text-sm p-2 w-full focus:outline-none focus:ring-2 focus:ring-secondary">
        </div>
    </div>
    <div class="flex flex-wrap gap-3">
        <button onclick="saveSettings()"
            class="bg-primary hover:bg-secondary text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors shadow">
            Save Schedule
        </button>
        <button onclick="confirmReset()"
            class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors shadow">
            🗑 Reset All Votes
        </button>
    </div>
    <p id="settings-msg" class="text-sm mt-3 hidden"></p>
</div>

@push('scripts')
<script>
async function saveSettings() {
    const start = document.getElementById('start_time').value;
    const end   = document.getElementById('end_time').value;
    if (!start || !end) { alert('Please fill in both start and end times.'); return; }

    const res = await apiFetch('{{ route("admin.settings.update") }}', {
        method: 'POST',
        body: JSON.stringify({ start_time: start, end_time: end }),
    });

    const msg = document.getElementById('settings-msg');
    msg.textContent = res.success ? '✅ Schedule saved!' : '❌ ' + res.message;
    msg.className = 'text-sm mt-3 ' + (res.success ? 'text-green-600' : 'text-red-600');
    msg.classList.remove('hidden');
    setTimeout(() => msg.classList.add('hidden'), 3000);
}

async function confirmReset() {
    if (!confirm('⚠️ Are you sure you want to RESET ALL VOTES? This cannot be undone!')) return;
    const res = await apiFetch('{{ route("admin.results.reset") }}', { method: 'POST' });
    alert(res.success ? res.message : 'Error: ' + res.message);
    if (res.success) location.reload();
}
</script>
@endpush
@endsection
