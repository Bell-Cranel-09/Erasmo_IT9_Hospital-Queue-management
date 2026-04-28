@extends('layouts.app')

@section('title', 'Doctor Schedule – St. Gabriel Medical Center')
@section('page-title', 'Manage Schedule')

@section('content')

<div class="max-w-3xl mx-auto space-y-5">

    {{-- Doctor info card --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center text-white font-bold text-xl flex-shrink-0">
            {{ strtoupper(substr($doctor->first_name, 0, 1)) }}
        </div>
        <div>
            <h2 class="text-lg font-semibold text-slate-800">{{ $doctor->full_name }}</h2>
            <p class="text-sm text-slate-500">{{ $doctor->specialization }} · {{ $doctor->department->name }}</p>
        </div>
        <a href="{{ route('doctors.index') }}" class="ml-auto text-sm text-blue-600 hover:text-blue-700 font-medium">← Back</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Current schedules --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800 text-sm">Current Schedules</h3>
            </div>

            @if($schedules->isEmpty())
            <div class="text-center py-10">
                <p class="text-sm text-slate-400">No schedules set yet</p>
            </div>
            @else
            <div class="divide-y divide-slate-50">
                @foreach($schedules as $schedule)
                <div class="px-5 py-3.5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ $schedule->day_name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} –
                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                        </p>
                        <p class="text-xs text-slate-400">
                            {{ $schedule->slot_duration_minutes }}min slots · Max {{ $schedule->max_patients }} patients
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($schedule->is_active)
                            <span class="text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full font-medium">Active</span>
                        @else
                            <span class="text-xs text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full font-medium">Inactive</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Add schedule form --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-blue-600 to-blue-700">
                <h3 class="font-semibold text-white text-sm">Add New Schedule</h3>
            </div>
            <form method="POST" action="{{ route('doctors.schedules.store', $doctor) }}" class="p-5 space-y-4">
                @csrf

                @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl px-3 py-2">
                    @foreach($errors->all() as $error)
                        <p class="text-xs text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Day of Week</label>
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
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Start Time</label>
                        <input name="start_time" type="time" required value="{{ old('start_time', '08:00') }}"
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">End Time</label>
                        <input name="end_time" type="time" required value="{{ old('end_time', '17:00') }}"
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Slot Duration (min)</label>
                        <select name="slot_duration_minutes" required
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
                               value="{{ old('max_patients', 10) }}" required
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-all shadow-lg shadow-blue-500/25 hover:-translate-y-0.5">
                    Add Schedule
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
