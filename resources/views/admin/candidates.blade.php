@extends('layouts.admin')
@section('page-title', 'Candidates Management')
@section('page-subtitle', 'Manage all candidates and their affiliations.')
@php $activeView = 'candidates'; @endphp

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h2 class="font-bold text-primary">All Candidates</h2>
        <button onclick="openModal()"
            class="bg-primary hover:bg-secondary text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors shadow">
            + Add Candidate
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-widest text-primary bg-slate-50">Photo</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-widest text-primary bg-slate-50">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-widest text-primary bg-slate-50">Party List</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-widest text-primary bg-slate-50">Position</th>
                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-widest text-primary bg-slate-50">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($candidates as $c)
                <tr class="hover:bg-gray-50 transition-colors" id="cand-row-{{ $c->id }}">
                    <td class="px-6 py-4">
                        @if($c->image_url)
                            <img src="{{ $c->image_url }}" class="h-10 w-10 rounded-full object-cover border border-gray-200">
                        @else
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-sm">
                                {{ strtoupper(substr($c->name, 0, 1)) }}
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium text-sm text-primary">{{ $c->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $c->party_list ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $c->position->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-center space-x-3">
                        <button onclick="openModal({{ json_encode(['id'=>$c->id,'name'=>$c->name,'party_list'=>$c->party_list,'position_id'=>$c->position_id]) }})"
                            class="text-blue-600 hover:text-blue-800 text-xs font-semibold">Edit</button>
                        <button onclick="deleteCandidate({{ $c->id }}, '{{ addslashes($c->name) }}')"
                            class="text-red-500 hover:text-red-700 text-xs font-semibold">Delete</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">No candidates yet. Add positions first.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal (uses multipart form for file upload) --}}
<div id="cand-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b">
            <h3 class="font-bold text-primary" id="modal-title">Add Candidate</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form id="cand-form" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Full Name</label>
                    <input type="text" name="name" id="c-name"
                        class="border border-gray-300 rounded-lg text-sm p-2.5 w-full focus:outline-none focus:ring-2 focus:ring-secondary"
                        placeholder="Juan Dela Cruz" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Party List</label>
                    <input type="text" name="party_list" id="c-party_list"
                        class="border border-gray-300 rounded-lg text-sm p-2.5 w-full focus:outline-none focus:ring-2 focus:ring-secondary"
                        placeholder="Independent">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Position</label>
                    <select name="position_id" id="c-position_id"
                        class="border border-gray-300 rounded-lg text-sm p-2.5 w-full focus:outline-none focus:ring-2 focus:ring-secondary" required>
                        <option value="">— Select Position —</option>
                        @foreach($positions as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Photo (optional, max 2MB)</label>
                    <input type="file" name="image" id="c-image" accept="image/*"
                        class="border border-gray-300 rounded-lg text-sm p-2 w-full focus:outline-none">
                    <p class="text-xs text-gray-400 mt-1">Leave blank to keep existing photo when editing.</p>
                </div>
                <p id="modal-error" class="text-red-600 text-xs hidden"></p>
            </div>
            <div class="flex justify-end gap-3 p-6 border-t">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2 text-sm bg-primary hover:bg-secondary text-white font-semibold rounded-xl transition">Save</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openModal(data = null) {
    document.getElementById('cand-modal').classList.remove('hidden');
    document.getElementById('modal-title').textContent = data ? 'Edit Candidate' : 'Add Candidate';
    document.getElementById('modal-error').classList.add('hidden');

    const form = document.getElementById('cand-form');
    if (data) {
        form.action = `/admin/candidates/${data.id}`;
        document.getElementById('form-method').value = 'POST'; // Laravel method spoofing handled via hidden _method
        form.querySelector('[name="_method"]').value = 'POST'; // We route POST for file upload
    } else {
        form.action = '{{ route("admin.candidates.store") }}';
        document.getElementById('form-method').value = 'POST';
    }

    document.getElementById('c-name').value        = data?.name ?? '';
    document.getElementById('c-party_list').value  = data?.party_list ?? '';
    document.getElementById('c-position_id').value = data?.position_id ?? '';
    document.getElementById('c-image').value       = '';
}
function closeModal() { document.getElementById('cand-modal').classList.add('hidden'); }

document.getElementById('cand-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const res = await fetch(this.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': window.csrfToken },
        body: formData,
    });
    const data = await res.json();
    if (data.success) { closeModal(); location.reload(); }
    else {
        const err = document.getElementById('modal-error');
        err.textContent = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Error saving.');
        err.classList.remove('hidden');
    }
});

async function deleteCandidate(id, name) {
    if (!confirm(`Delete candidate "${name}"?`)) return;
    const res = await apiFetch(`/admin/candidates/${id}`, { method: 'DELETE' });
    if (res.success) document.getElementById(`cand-row-${id}`)?.remove();
    else alert(res.message);
}
</script>
@endpush
@endsection
