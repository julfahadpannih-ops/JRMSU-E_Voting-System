@extends('layouts.admin')
@section('page-title', 'Election Settings')
@section('page-subtitle', 'Configure election time window.')
@php $activeView = 'settings'; @endphp

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-lg">
    <h3 class="font-bold text-primary text-lg mb-4">Election Schedule</h3>

    @if(session('success'))
        <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
            <input type="datetime-local" name="start_time"
                value="{{ $start_time ? date('Y-m-d\TH:i', strtotime($start_time)) : '' }}"
                class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
            <input type="datetime-local" name="end_time"
                value="{{ $end_time ? date('Y-m-d\TH:i', strtotime($end_time)) : '' }}"
                class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2 rounded-xl transition-colors">
            💾 Save Settings
        </button>
    </form>
</div>
@endsection