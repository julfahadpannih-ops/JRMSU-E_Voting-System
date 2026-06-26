@extends('layouts.admin')
@section('page-title', 'Audit Logs')
@section('page-subtitle', 'Full activity history of the system.')
@php $activeView = 'audit-logs'; @endphp

@section('content')

{{-- Filters --}}
<form method="GET" action="{{ route('admin.audit-logs.index') }}" class="bg-white rounded-2xl shadow-sm p-5 mb-6 border border-gray-100 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Actor Type</label>
        <select name="actor_type" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-secondary">
            <option value="">All</option>
            <option value="admin"  {{ request('actor_type') === 'admin'  ? 'selected' : '' }}>Admin</option>
            <option value="voter"  {{ request('actor_type') === 'voter'  ? 'selected' : '' }}>Voter</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Action Contains</label>
        <input type="text" name="action" value="{{ request('action') }}"
            placeholder="e.g. vote, login"
            class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-secondary w-48">
    </div>
    <button type="submit" class="bg-primary text-white text-sm font-semibold px-5 py-2 rounded-xl hover:bg-secondary transition-colors">
        Filter
    </button>
    <a href="{{ route('admin.audit-logs.index') }}" class="text-sm text-gray-400 hover:text-gray-700 py-2">Clear</a>
</form>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wide border-b border-gray-200">
                    <th class="px-5 py-3 text-left">#</th>
                    <th class="px-5 py-3 text-left">Action</th>
                    <th class="px-5 py-3 text-left">Actor</th>
                    <th class="px-5 py-3 text-left">Type</th>
                    <th class="px-5 py-3 text-left">Details</th>
                    <th class="px-5 py-3 text-left">IP Address</th>
                    <th class="px-5 py-3 text-left">Timestamp</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                @php
                    $badgeColor = match(true) {
                        str_contains($log->action, 'failed')   => 'bg-red-100 text-red-700',
                        str_contains($log->action, 'deleted')  => 'bg-red-100 text-red-700',
                        str_contains($log->action, 'rejected') => 'bg-red-100 text-red-700',
                        str_contains($log->action, 'reset')    => 'bg-orange-100 text-orange-700',
                        str_contains($log->action, 'login')    => 'bg-blue-100 text-blue-700',
                        str_contains($log->action, 'vote')     => 'bg-green-100 text-green-700',
                        str_contains($log->action, 'approved') => 'bg-green-100 text-green-700',
                        default                                => 'bg-gray-100 text-gray-600',
                    };
                    $details = $log->details ? json_decode($log->details, true) : [];
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $log->id }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeColor }}">
                            {{ str_replace('_', ' ', $log->action) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $log->actor_name }}</td>
                    <td class="px-5 py-3">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                            {{ $log->actor_type === 'admin' ? 'bg-indigo-100 text-indigo-700' : 'bg-teal-100 text-teal-700' }}">
                            {{ ucfirst($log->actor_type) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-500 text-xs max-w-xs">
                        @if($details)
                            <details class="cursor-pointer">
                                <summary class="text-primary hover:underline text-xs">View details</summary>
                                <pre class="mt-1 text-[10px] bg-gray-50 p-2 rounded overflow-auto max-h-32">{{ json_encode($details, JSON_PRETTY_PRINT) }}</pre>
                            </details>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-400 text-xs font-mono">{{ $log->ip_address ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-400 text-xs whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($log->created_at)->timezone('Asia/Manila')->format('M j, Y g:i:s A') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400">No audit logs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
