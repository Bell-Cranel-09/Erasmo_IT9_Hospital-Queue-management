@extends('layouts.app')

@section('title', 'Patient Queue – St. Gabriel Medical Center')
@section('page-title', 'Patient Queue')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── LEFT COLUMN ──────────────────────────────────────────────── --}}
    <div class="space-y-4">

        {{-- Now Serving display --}}
        <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-6 text-center shadow-xl">
            <p class="text-blue-200 text-sm font-medium mb-2 uppercase tracking-widest">Now Serving</p>
            <p class="text-white text-6xl font-display font-bold mb-1">
                {{ $stats['current']?->queue_number ?? '—' }}
            </p>
            @if($stats['current'])
                <p class="text-blue-200 text-sm">{{ $stats['current']->queue_code }}</p>
                <p class="text-white font-medium mt-1 text-sm truncate px-4">{{ $stats['current']->patient->full_name }}</p>
            @else
                <p class="text-blue-300 text-sm">No patient being served</p>
            @endif
        </div>

        {{-- Stats grid --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <h3 class="text-sm font-semibold text-slate-600 mb-3 uppercase tracking-wide">Today's Summary</h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-amber-50 rounded-xl p-3 text-center">
                    <p class="text-2xl font-bold text-amber-700">{{ $stats['waiting'] }}</p>
                    <p class="text-xs text-amber-600 font-medium">Waiting</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-3 text-center">
                    <p class="text-2xl font-bold text-blue-700">{{ $stats['serving'] }}</p>
                    <p class="text-xs text-blue-600 font-medium">Serving</p>
                </div>
                <div class="bg-green-50 rounded-xl p-3 text-center">
                    <p class="text-2xl font-bold text-green-700">{{ $stats['done'] }}</p>
                    <p class="text-xs text-green-600 font-medium">Done</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3 text-center">
                    <p class="text-2xl font-bold text-slate-700">{{ $stats['total'] }}</p>
                    <p class="text-xs text-slate-600 font-medium">Total</p>
                </div>
            </div>
        </div>

        {{-- Staff actions --}}
        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())

        {{-- Call Next --}}
        <form method="POST" action="{{ route('queue.call-next', $department) }}">
            @csrf
            <button type="submit"
                    class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-green-500/25 hover:-translate-y-0.5 flex items-center justify-center gap-2 text-base">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Call Next Patient
            </button>
        </form>

        {{-- Add walk-in --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">Add Walk-in Patient</h3>
            <form method="POST" action="{{ route('queue.join') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Patient</label>
                    <select name="patient_id" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Select patient...</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->full_name }} ({{ $patient->patient_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Department</label>
                    <select name="department_id" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $dept->id == $department->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add to Queue
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- ── RIGHT COLUMN: Queue list ──────────────────────────────────── --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

            {{-- Header --}}
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-slate-800">{{ $department->name }} Queue</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ now()->format('F j, Y') }}</p>
                </div>
                <form method="GET" action="{{ route('queue.index') }}">
                    <select name="department" onchange="this.form.submit()"
                            class="text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $dept->id == $department->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- Queue rows --}}
            @if($queues->isEmpty())
            <div class="text-center py-16">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-slate-500 font-medium">No patients in queue today</p>
            </div>
            @else
            <div class="divide-y divide-slate-50">
                @foreach($queues as $queue)
                <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 transition-colors
                            {{ $queue->status === 'serving' ? 'bg-blue-50/60 border-l-4 border-l-blue-500' : '' }}">

                    {{-- Number badge --}}
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 font-bold text-lg
                                {{ $queue->status === 'serving' ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' : 'bg-slate-100 text-slate-600' }}">
                        {{ $queue->queue_number }}
                    </div>

                    {{-- Patient info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $queue->patient->full_name }}</p>
                            @if($queue->status === 'serving')
                                <span class="flex items-center gap-1 text-xs text-blue-600 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                    Now Serving
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $queue->queue_code }}
                            @if($queue->called_at)· Called {{ $queue->called_at->format('h:i A') }}@endif
                        </p>
                    </div>

                    {{-- Status badge --}}
                    <span class="badge-{{ $queue->status }} text-xs font-semibold px-3 py-1 rounded-full flex-shrink-0 capitalize">
                        {{ $queue->status }}
                    </span>

                    {{-- Delete button (admin/staff only) --}}
                    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                    <form method="POST" action="{{ route('queue.destroy', $queue) }}"
                          onsubmit="return confirm('Remove {{ $queue->patient->full_name }} from queue?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all flex-shrink-0"
                                title="Remove from queue">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>setTimeout(() => location.reload(), 30000);</script>
@endpush

@endsection
