@extends('layouts.app')

@section('title', 'Doctor Schedule – St. Gabriel Medical Center')
@section('page-title', 'Manage Schedule')

@section('content')

<div class="max-w-3xl mx-auto space-y-5">

    {{-- Doctor info --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center text-white font-bold text-xl flex-shrink-0">
            {{ strtoupper(substr($doctor->first_name, 0, 1)) }}
        </div>
        <div>
            <h2 class="text-lg font-semibold text-slate-800">{{ $doctor->full_name }}</h2>
            <p class="text-sm text-slate-500">{{ $doctor->specialization }} · {{ $doctor->department->name }}</p>
        </div>
        <a href="{{ route('doctors.index') }}"
           class="ml-auto text-sm text-blue-600 hover:text-blue-700 font-medium bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-xl transition">
            ← Back
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- ── Current Schedules ──────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800 text-sm">Schedules</h3>
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1 text-xs text-green-600">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span> Active
                    </span>
                    <span class="flex items-center gap-1 text-xs text-slate-400">
                        <span class="w-2 h-2 rounded-full bg-slate-300"></span> Inactive
                    </span>
                </div>
            </div>

            @if($schedules->isEmpty())
            <div class="text-center py-12">
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-400">No schedules yet</p>
                <p class="text-xs text-slate-300 mt-1">Add one using the form →</p>
            </div>
            @else
            <div class="divide-y divide-slate-50">
                @foreach($schedules as $schedule)
                <div class="px-5 py-3.5 flex items-center gap-3 hover:bg-slate-50 transition-colors
                            {{ !$schedule->is_active ? 'opacity-60' : '' }}">

                    {{-- Day badge --}}
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                                {{ $schedule->is_active ? 'bg-blue-50' : 'bg-slate-100' }}">
                        <span class="text-xs font-bold {{ $schedule->is_active ? 'text-blue-600' : 'text-slate-400' }}">
                            {{ strtoupper(substr($schedule->day_name, 0, 3)) }}
                        </span>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800">{{ $schedule->day_name }}</p>
                        <p class="text-xs text-slate-500">
                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} –
                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                        </p>
                        <p class="text-xs text-slate-400">
                            {{ $schedule->slot_duration_minutes }}min slots · Max {{ $schedule->max_patients }}
                        </p>
                    </div>

                    {{-- Status + Action button --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if($schedule->is_active)
                            <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Active</span>

                            {{-- Deactivate button (data stays, just marked inactive) --}}
                            <form method="POST"
                                  action="{{ route('doctors.schedules.destroy', [$doctor, $schedule]) }}"
                                  onsubmit="return confirm('Deactivate {{ $schedule->day_name }} schedule? The data will be kept and can be viewed in history.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-amber-500 hover:bg-amber-50 transition-all"
                                        title="Deactivate schedule (data preserved)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                            </form>
                        @else
                            <span class="text-xs font-medium text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">Inactive</span>
                            {{-- Reactivate button --}}
                            <form method="POST" action="{{ route('doctors.schedules.restore', [$doctor, $schedule]) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-green-500 hover:bg-green-50 transition-all"
                                        title="Reactivate schedule">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Legend note --}}
            <div class="px-5 py-3 bg-slate-50 border-t border-slate-100">
                <p class="text-xs text-slate-400 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Deactivating a schedule keeps all data and appointment history intact.
                </p>
            </div>
            @endif
        </div>

        {{-- ── Add Schedule Form ──────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-blue-600 to-blue-700">
                <h3 class="font-semibold text-white text-sm">Add New Schedule</h3>
                <p class="text-blue-200 text-xs mt-0.5">Only active schedules appear for booking</p>
            </div>

            <form method="POST" action="{{ route('doctors.schedules.store', $doctor) }}" class="p-5 space-y-4">
                @csrf

                @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl px-3 py-2.5">
                    @foreach($errors->all() as $error)
                        <p class="text-xs text-red-600 flex items-center gap-1">
                            <span class="w-1 h-1 rounded-full bg-red-400 flex-shrink-0"></span>
                            {{ $error }}
                        </p>
                    @endforeach
                </div>
                @endif

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Day of Week <span class="text-red-400">*</span></label>
                    <select name="day_of_week" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        @foreach($days as $num => $name)
                            <option value="{{ $num }}" {{ old('day_of_week') == $num ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Start Time <span class="text-red-400">*</span></label>
                        <input name="start_time" type="time" required value="{{ old('start_time', '08:00') }}"
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">End Time <span class="text-red-400">*</span></label>
                        <input name="end_time" type="time" required value="{{ old('end_time', '17:00') }}"
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Slot Duration</label>
                        <select name="slot_duration_minutes"
                                class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="15">15 minutes</option>
                            <option value="20">20 minutes</option>
                            <option value="30" selected>30 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60">60 minutes</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Max Patients</label>
                        <input name="max_patients" type="number" min="1" max="100"
                               value="{{ old('max_patients', 10) }}"
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-all shadow-lg shadow-blue-500/25 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Schedule
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
