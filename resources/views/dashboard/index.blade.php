@extends('layouts.app')

@section('title', 'Dashboard – St. Gabriel Medical Center')
@section('page-title', 'Dashboard')

@section('content')

{{-- Welcome banner --}}
<div class="relative bg-gradient-to-r from-blue-700 via-blue-600 to-blue-500 rounded-2xl p-6 mb-6 overflow-hidden shadow-xl">
    {{-- Decorative circles --}}
    <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-white/10"></div>
    <div class="absolute -bottom-6 right-20 w-24 h-24 rounded-full bg-white/10"></div>
    <div class="absolute top-4 right-32 w-8 h-8 rounded-full bg-white/20"></div>

    <div class="relative z-10">
        <p class="text-blue-100 text-sm font-medium mb-1">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }},</p>
        <h2 class="text-white text-2xl font-display mb-1">{{ auth()->user()->name }}</h2>
        <p class="text-blue-200 text-sm">Here's what's happening at the clinic today.</p>
    </div>
</div>

{{-- ── STATS CARDS ──────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Total Patients --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">↑ All time</span>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ $totalPatients }}</p>
        <p class="text-sm text-slate-500 mt-0.5">Total Patients</p>
    </div>

    {{-- Today's Appointments --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Today</span>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ $todayAppointments }}</p>
        <p class="text-sm text-slate-500 mt-0.5">Appointments</p>
    </div>

    {{-- Queue Waiting --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full animate-pulse-soft">Live</span>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ $waitingCount }}</p>
        <p class="text-sm text-slate-500 mt-0.5">Waiting in Queue</p>
    </div>

    {{-- Currently Serving --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full animate-pulse-soft">Now</span>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ $currentQueue?->queue_code ?? '—' }}</p>
        <p class="text-sm text-slate-500 mt-0.5">Now Serving</p>
    </div>
</div>

{{-- ── BOTTOM SECTION ───────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <a href="{{ route('queue.index') }}"
               class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 hover:bg-blue-100 transition-all group">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-800">Add to Queue</p>
                    <p class="text-xs text-slate-500">Register walk-in patient</p>
                </div>
            </a>

            <a href="{{ route('appointments.create') }}"
               class="flex items-center gap-3 p-3 rounded-xl bg-purple-50 hover:bg-purple-100 transition-all group">
                <div class="w-8 h-8 rounded-lg bg-purple-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-800">Book Appointment</p>
                    <p class="text-xs text-slate-500">Schedule a doctor visit</p>
                </div>
            </a>

            <a href="{{ route('patients.create') }}"
               class="flex items-center gap-3 p-3 rounded-xl bg-green-50 hover:bg-green-100 transition-all group">
                <div class="w-8 h-8 rounded-lg bg-green-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-800">Register Patient</p>
                    <p class="text-xs text-slate-500">Add new patient record</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Today's Queue Preview --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-700">Today's Queue</h3>
            <a href="{{ route('queue.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View all →</a>
        </div>

        @if($recentQueues->isEmpty())
        <div class="text-center py-8">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm text-slate-500">No patients in queue today</p>
        </div>
        @else
        <div class="space-y-2">
            @foreach($recentQueues as $queue)
            <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">
                    {{ $queue->queue_number }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800 truncate">{{ $queue->patient->full_name }}</p>
                    <p class="text-xs text-slate-500">{{ $queue->department->name }} · {{ $queue->queue_code }}</p>
                </div>
                <span class="badge-{{ $queue->status }} text-xs font-semibold px-2.5 py-1 rounded-full flex-shrink-0 capitalize">
                    {{ $queue->status }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@endsection
