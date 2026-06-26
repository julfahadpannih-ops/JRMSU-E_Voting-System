@extends('layouts.admin')
@section('page-title', 'Voters Management')
@section('page-subtitle', 'Manage student registration and voting status.')
@php $activeView = 'voters'; @endphp

@section('content')

{{-- Pending approvals --}}
@php $pending = $voters->where('is_approved', false); @endphp
@if($pending->count() > 0)
<div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 mb-6">
    <h3 class="font-bold text-yellow-800 mb-3 text-sm">⏳ Pending Registrations ({{ $pending->count() }})</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-xs uppercase text-yellow-700">
                    <th class="text-left px-3 py-2">Student ID</th>
                    <th class="text-left px-3 py-2">Name</th>
                    <th class="text-left px-3 py-2">Course</th>
                    <th class="text-center px-3 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pending as $v)
                <tr class="border-t border-yellow-100">
                    <td class="px-3 py-2 font-mono text-xs">{{ $v->student_id }}</td>
                    <td class="px-3 py-2">{{ $v->name }}</td>
                    <td class="px-3 py-2">{{ $v->course }}</td>
                    <td class="px-3 py-2 text-center space-x-2">
                        <button onclick="approveVoter({{ $v->id }})"
                            class="text-xs bg-green-600 text-white px-3 py-1 rounded-lg hover:bg-green-700 transition">Approve</button>
                        <button onclick="rejectVoter({{ $v->id }})"
                            class="text-xs bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition">Reject</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Main table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h2 class="font-bold text-primary">Approved Voters</h2>
        <button onclick="openVoterModal()"
            class="bg-primary hover:bg-secondary text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors shadow">
            + Add Voter
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100" id="voters-table">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-widest text-primary bg-slate-50">Student ID</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-widest text-primary bg-slate-50">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-widest text-primary bg-slate-50">Course</th>
                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-widest text-primary bg-slate-50">Voted</th>
                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-widest text-primary bg-slate-50">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" id="voters-tbody">
                @forelse($voters->where('is_approved', true) as $voter)
                <tr class="hover:bg-gray-50 transition-colors" id="voter-row-{{ $voter->id }}">
                    <td class="px-6 py-4 text-xs font-mono text-gray-500">{{ $voter->student_id }}</td>
                    <td class="px-6 py-4 font-medium text-sm">{{ $voter->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $voter->course }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($voter->has_voted)
                            <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full">Voted</span>
                        @else
                            <span class="inline-block bg-gray-100 text-gray-500 text-xs px-2 py-1 rounded-full">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center space-x-3">
                        <button onclick="openVoterModal({{ json_encode(['id'=>$voter->id,'student_id'=>$voter->student_id,'name'=>$voter->name,'course'=>$voter->course]) }})"
                            class="text-blue-600 hover:text-blue-800 text-xs font-semibold transition">Edit</button>
                        <button onclick="deleteVoter({{ $voter->id }}, '{{ addslashes($voter->name) }}')"
                            class="text-red-500 hover:text-red-700 text-xs font-semibold transition">Delete</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">No approved voters yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal --}}
<div id="voter-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b">
            <h3 class="font-bold text-primary" id="modal-title">Add Voter</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <div class="p-6 space-y-4">
            <input type="hidden" id="voter-id">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Student ID</label>
                <input type="text" id="v-student_id" class="border border-gray-300 rounded-lg text-sm p-2.5 w-full focus:outline-none focus:ring-2 focus:ring-secondary" placeholder="2021-00001">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Full Name</label>
                <input type="text" id="v-name" class="border border-gray-300 rounded-lg text-sm p-2.5 w-full focus:outline-none focus:ring-2 focus:ring-secondary" placeholder="Juan Dela Cruz">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Course</label>
                <input type="text" id="v-course" class="border border-gray-300 rounded-lg text-sm p-2.5 w-full focus:outline-none focus:ring-2 focus:ring-secondary" placeholder="BSIS">
            </div>
            <p id="modal-error" class="text-red-600 text-xs hidden"></p>
        </div>
        <div class="flex justify-end gap-3 p-6 border-t">
            <button onclick="closeModal()" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-xl transition">Cancel</button>
            <button onclick="saveVoter()" class="px-5 py-2 text-sm bg-primary hover:bg-secondary text-white font-semibold rounded-xl transition">Save</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openVoterModal(data = null) {
    document.getElementById('voter-modal').classList.remove('hidden');
    document.getElementById('modal-title').textContent = data ? 'Edit Voter' : 'Add Voter';
    document.getElementById('voter-id').value      = data?.id ?? '';
    document.getElementById('v-student_id').value  = data?.student_id ?? '';
    document.getElementById('v-name').value        = data?.name ?? '';
    document.getElementById('v-course').value      = data?.course ?? '';
    document.getElementById('modal-error').classList.add('hidden');
}
function closeModal() { document.getElementById('voter-modal').classList.add('hidden'); }

async function saveVoter() {
    const id = document.getElementById('voter-id').value;
    const payload = {
        student_id: document.getElementById('v-student_id').value.trim(),
        name:       document.getElementById('v-name').value.trim(),
        course:     document.getElementById('v-course').value.trim(),
    };
    const url    = id ? `/admin/voters/${id}` : '{{ route("admin.voters.store") }}';
    const method = id ? 'PUT' : 'POST';
    const res    = await apiFetch(url, { method, body: JSON.stringify(payload) });
    if (res.success) { closeModal(); location.reload(); }
    else {
        const err = document.getElementById('modal-error');
        err.textContent = res.message || res.errors ? Object.values(res.errors ?? {}).flat().join(' ') : 'Error saving.';
        err.classList.remove('hidden');
    }
}

async function deleteVoter(id, name) {
    if (!confirm(`Delete voter "${name}"?`)) return;
    const res = await apiFetch(`/admin/voters/${id}`, { method: 'DELETE' });
    if (res.success) document.getElementById(`voter-row-${id}`)?.remove();
    else alert(res.message);
}

async function approveVoter(id) {
    const res = await apiFetch(`/admin/voters/${id}/approve`, { method: 'PATCH' });
    if (res.success) location.reload();
    else alert(res.message);
}

async function rejectVoter(id) {
    if (!confirm('Reject and remove this registration?')) return;
    const res = await apiFetch(`/admin/voters/${id}/reject`, { method: 'DELETE' });
    if (res.success) location.reload();
    else alert(res.message);
}
</script>
@endpush
@endsection
