@extends('layouts.app')
@section('title', 'JRMSU SSG E-Voting — Voter Portal')

@section('body')
<div class="min-h-screen bg-gray-50">

    {{-- Header --}}
    <header class="bg-primary text-white shadow-lg sticky top-0 z-20">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/SSG.jpg') }}" alt="SSG" class="h-10 w-10 rounded-full object-cover border-2 border-secondary">
                <div>
                    <div class="font-bold text-sm leading-none">JRMSU Siocon SSG</div>
                    <div class="text-[10px] text-secondary uppercase tracking-widest">E-Voting System</div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="hidden sm:block text-sm text-white/80">
                    {{ $voter->name }}
                    @if($voter->has_voted)
                        <span class="ml-2 text-xs bg-green-500 text-white px-2 py-0.5 rounded-full font-semibold">✓ Voted</span>
                    @endif
                </span>
                <form method="POST" action="{{ route('voter.logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-white/70 hover:text-white border border-white/30 px-3 py-1.5 rounded-lg transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">

        {{-- Flash: already voted --}}
        @if(session('voted') || $voter->has_voted)
        <div class="bg-green-50 border border-green-200 rounded-2xl p-6 mb-8 text-center">
            <div class="text-5xl mb-3">🗳️</div>
            <h2 class="text-2xl font-bold text-green-700 mb-2">Your vote has been recorded!</h2>
            <p class="text-green-600 text-sm">Thank you, {{ $voter->name }}. Your ballot has been successfully submitted.</p>
        </div>

        {{-- Show live results to voter after voting --}}
        <div class="mb-4 flex items-center justify-between">
            <h3 class="font-bold text-primary text-lg">📊 Live Results</h3>
            <span class="text-xs text-gray-400">Updates every 10 seconds</span>
        </div>
        <div id="voter-results" class="space-y-4">
            <p class="text-gray-400 text-sm text-center py-8">Loading…</p>
        </div>

        @elseif(! $status['open'])
        {{-- Voting closed --}}
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center border-t-4 border-red-500 mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-600 mb-4 text-3xl">🔒</div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Voting Unavailable</h2>
            <p class="text-gray-600">{{ $status['message'] }}</p>
        </div>

        @else
        {{-- Ballot form --}}
        <div class="mb-6">
            <h2 class="text-xl font-bold text-primary">Official Ballot</h2>
            <p class="text-sm text-gray-500">Select exactly one candidate per position. All positions are required.</p>
        </div>

        <form method="POST" action="{{ route('voter.vote') }}" id="ballot-form">
            @csrf
            <div class="space-y-6">
                @foreach($positions as $position)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100" data-position="{{ $position->name }}">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center sticky top-16 z-10">
                        <h3 class="text-lg font-bold text-primary">{{ $position->name }}</h3>
                        <span class="text-xs bg-secondary text-white px-3 py-1 rounded-full shadow-sm font-semibold">
                            Select {{ $position->max_votes }}
                        </span>
                    </div>
                    <div class="p-6 grid grid-cols-1 xl:grid-cols-2 gap-4">
                        @forelse($position->candidates as $candidate)
                        <label class="cursor-pointer group flex h-full">
                            @if($position->max_votes > 1)
                            <input type="checkbox"
                                name="votes[{{ $position->id }}][]"
                                value="{{ $candidate->id }}"
                                class="sr-only jrmsu-choice"
                                data-position="{{ $position->id }}"
                                data-max="{{ $position->max_votes }}">
                            @else
                            <input type="radio"
                                name="votes[{{ $position->id }}]"
                                value="{{ $candidate->id }}"
                                class="sr-only jrmsu-choice"
                                data-position="{{ $position->id }}"
                                data-max="{{ $position->max_votes }}"
                                required>
                            @endif
                            <div class="w-full p-4 rounded-xl border-2 border-gray-200 group-hover:border-secondary group-hover:shadow-md
                                        transition-all flex items-center bg-white shadow-sm ballot-card">
                                @if($candidate->image_url)
                                    <img src="{{ $candidate->image_url }}" alt="{{ $candidate->name }}"
                                         class="h-16 w-16 md:h-20 md:w-20 rounded-full object-cover mr-4 shrink-0 border border-gray-200 shadow-sm">
                                @else
                                    <div class="h-16 w-16 md:h-20 md:w-20 rounded-full bg-gray-200 text-gray-600 flex items-center
                                                justify-center font-bold mr-4 shrink-0 group-hover:bg-secondary group-hover:text-white
                                                transition-colors shadow-sm text-2xl">
                                        {{ strtoupper(substr($candidate->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-gray-800 text-base md:text-lg truncate candidate-name">{{ $candidate->name }}</div>
                                    <div class="text-sm text-gray-500 truncate">{{ $candidate->party_list ?? 'Independent' }}</div>
                                </div>
                                <div class="check-icon opacity-0 scale-50 transition-all duration-200 text-green-600 ml-2 shrink-0">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </label>
                        @empty
                        <p class="text-gray-400 italic col-span-2 text-sm">No candidates for this position.</p>
                        @endforelse
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-10 flex justify-end">
                <button type="button" onclick="openBallotConfirmModal()"
                    class="w-full sm:w-auto bg-gradient-to-r from-primary to-secondary text-white text-lg font-bold
                           py-4 px-10 rounded-xl shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-200">
                    Submit Official Ballot
                </button>
            </div>
        </form>

        {{-- Ballot Confirmation Modal --}}
        <div id="ballot-confirm-modal"
             class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center">
                <div class="text-5xl mb-4">🗳️</div>
                <h2 class="text-2xl font-bold text-primary mb-2">Confirm Your Ballot</h2>
                <p class="text-gray-600 text-sm mb-2">
                    You are about to submit your <strong>official ballot</strong>.
                </p>
                <p class="text-red-500 text-sm font-semibold mb-6">
                    ⚠️ This action is <u>permanent</u> and cannot be changed once submitted.
                </p>
                <div id="ballot-summary" class="text-left bg-gray-50 rounded-xl p-4 mb-6 text-sm text-gray-700 space-y-1 max-h-48 overflow-y-auto"></div>
                <div class="flex gap-3">
                    <button onclick="closeBallotConfirmModal()"
                        class="flex-1 border border-gray-300 text-gray-600 font-semibold py-3 rounded-xl hover:bg-gray-50 transition-colors">
                        ← Go Back
                    </button>
                    <button onclick="submitBallot()"
                        class="flex-1 bg-gradient-to-r from-primary to-secondary text-white font-bold py-3 rounded-xl hover:shadow-lg transition-all">
                        ✅ Submit Ballot
                    </button>
                </div>
            </div>
        </div>
        @endif
    </main>
</div>

@push('scripts')
<style>
    input[type="radio"].jrmsu-choice:checked + .ballot-card,
    input[type="checkbox"].jrmsu-choice:checked + .ballot-card {
        border-color: #DAA520;
        background-color: #fffbeb;
        box-shadow: 0 0 0 3px rgba(218,165,32,.2);
    }
    input[type="radio"].jrmsu-choice:checked + .ballot-card .check-icon,
    input[type="checkbox"].jrmsu-choice:checked + .ballot-card .check-icon {
        opacity: 1;
        transform: scale(1);
    }
</style>
<script>
// Ballot confirmation modal logic
function openBallotConfirmModal() {
    // Build summary of selections
    const form    = document.getElementById('ballot-form');
    const summary = document.getElementById('ballot-summary');
    const inputs  = form.querySelectorAll('input[type="radio"]:checked, input[type="checkbox"]:checked');

    if (inputs.length === 0) {
        alert('Please select at least one candidate before submitting.');
        return;
    }

    let html = '<ul class="space-y-1">';
    inputs.forEach(input => {
        const card  = input.nextElementSibling;
        const name  = card?.querySelector('.candidate-name')?.textContent?.trim() || input.value;
        const pos   = input.closest('.bg-white.rounded-xl')?.dataset?.position || '';
        html += `<li>✔️ <span class="font-medium">${name}</span>${pos ? ' <span class="text-gray-400 text-xs">— ' + pos + '</span>' : ''}</li>`;
    });
    html += '</ul>';
    summary.innerHTML = html || '<p class="text-gray-400">No selections detected.</p>';

    document.getElementById('ballot-confirm-modal').classList.remove('hidden');
    document.getElementById('ballot-confirm-modal').classList.add('flex');
}

function closeBallotConfirmModal() {
    document.getElementById('ballot-confirm-modal').classList.add('hidden');
    document.getElementById('ballot-confirm-modal').classList.remove('flex');
}

function submitBallot() {
    document.getElementById('ballot-form').submit();
}

// Close on backdrop click
document.getElementById('ballot-confirm-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeBallotConfirmModal();
});

// Live results (shown after voting)
@if(session('voted') || $voter->has_voted)
async function loadVoterResults() {
    const res = await fetch('{{ route("voter.results") }}');
    const data = await res.json();
    if (!data.success) return;

    const container = document.getElementById('voter-results');
    if (!data.data?.length) {
        container.innerHTML = '<p class="text-gray-400 text-sm text-center">No votes recorded yet.</p>';
        return;
    }

    const grouped = {};
    data.data.forEach(r => {
        if (!grouped[r.positionId]) grouped[r.positionId] = { positionName: r.positionName, candidates: [] };
        grouped[r.positionId].candidates.push(r);
    });

    container.innerHTML = '';
    Object.values(grouped).forEach(group => {
        const sorted = [...group.candidates].sort((a, b) => b.voteCount - a.voteCount);
        const topVotes = sorted[0]?.voteCount ?? 0;
        const totalVotes = sorted.reduce((s, c) => s + c.voteCount, 0);

        const bars = sorted.map(c => {
            const pct    = totalVotes > 0 ? Math.round(c.voteCount / totalVotes * 100) : 0;
            const isLeader = c.voteCount > 0 && c.voteCount === topVotes;
            const avatar = c.image
                ? `<img src="${c.image}" class="h-8 w-8 rounded-full object-cover border border-gray-200 shrink-0">`
                : `<div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center font-bold text-xs text-gray-600 shrink-0">${c.candidateName.charAt(0)}</div>`;
            return `
            <div class="mb-3">
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center space-x-2 min-w-0">
                        ${avatar}
                        <span class="text-sm font-medium text-gray-800 truncate">${isLeader ? '★ ' : ''}${c.candidateName}</span>
                        <span class="text-xs text-gray-400 truncate hidden sm:inline">${c.partyList || 'Ind.'}</span>
                    </div>
                    <div class="text-right ml-3 shrink-0">
                        <span class="font-bold text-primary text-sm">${c.voteCount}</span>
                        <span class="text-xs text-gray-400 ml-1">(${pct}%)</span>
                    </div>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="bg-secondary h-2.5 rounded-full transition-all duration-700" style="width:${pct}%"></div>
                </div>
            </div>`;
        }).join('');

        container.innerHTML += `
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h4 class="font-bold text-primary mb-4 text-sm uppercase tracking-wide">${group.positionName}</h4>
            ${bars}
        </div>`;
    });
}
loadVoterResults();
setInterval(loadVoterResults, 10000);
@endif
</script>
@endpush
@endsection
