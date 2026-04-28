@extends('layouts.app')

@section('title', 'Patient Profile – St. Gabriel Medical Center')
@section('page-title', 'Patient Profile')

@section('content')

<div class="max-w-4xl mx-auto space-y-5">

    {{-- Profile header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-700 to-blue-500 h-20"></div>
        <div class="px-6 pb-5">
            <div class="flex items-end gap-4 -mt-8 mb-4">
                <div class="w-16 h-16 rounded-2xl bg-white shadow-lg border-4 border-white flex items-center justify-center text-blue-600 font-bold text-2xl flex-shrink-0">
                    {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                </div>
                <div class="pb-1">
                    <h2 class="text-xl font-bold text-slate-800">{{ $patient->full_name }}</h2>
                    <p class="text-sm text-slate-500">{{ $patient->patient_code }}</p>
                </div>
                <div class="ml-auto pb-1 flex gap-2">
                    <a href="{{ route('patients.edit', $patient) }}"
                       class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('patients.index') }}"
                       class="text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-xl transition">
                        ← Back
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Date of Birth</p>
                    <p class="text-sm font-medium text-slate-700">{{ $patient->date_of_birth->format('M j, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Gender</p>
                    <p class="text-sm font-medium text-slate-700 capitalize">{{ $patient->gender }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Phone</p>
                    <p class="text-sm font-medium text-slate-700">{{ $patient->phone ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Email</p>
                    <p class="text-sm font-medium text-slate-700 truncate">{{ $patient->user->email }}</p>
                </div>
            </div>

            @if($patient->address)
            <div class="mt-3 pt-3 border-t border-slate-100">
                <p class="text-xs text-slate-400 mb-0.5">Address</p>
                <p class="text-sm text-slate-700">{{ $patient->address }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Recent Appointments --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800 text-sm">Appointments</h3>
                <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}"
                   class="text-xs font-medium text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg transition">
                    + Book
                </a>
            </div>

            @if($patient->appointments->isEmpty())
            <div class="text-center py-10">
                <p class="text-sm text-slate-400">No appointments yet</p>
            </div>
            @else
            <div class="divide-y divide-slate-50">
                @foreach($patient->appointments->take(5) as $appt)
                <div class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-slate-800 truncate">{{ $appt->doctor->full_name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $appt->appointment_date->format('M j, Y') }} ·
                            {{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}
                        </p>
                    </div>
                    <span class="badge-{{ $appt->status }} text-xs font-semibold px-2.5 py-1 rounded-full flex-shrink-0 capitalize">
                        {{ $appt->status }}
                    </span>
                </div>
                @endforeach
            </div>
            @if($patient->appointments->count() > 5)
            <div class="px-5 py-3 border-t border-slate-100 text-center">
                <a href="{{ route('appointments.index', ['patient_id' => $patient->id]) }}"
                   class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                    View all {{ $patient->appointments->count() }} appointments →
                </a>
            </div>
            @endif
            @endif
        </div>

        {{-- Queue History --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800 text-sm">Queue History</h3>
            </div>

            @if($patient->queues->isEmpty())
            <div class="text-center py-10">
                <p class="text-sm text-slate-400">No queue history yet</p>
            </div>
            @else
            <div class="divide-y divide-slate-50">
                @foreach($patient->queues->take(5) as $queue)
                <div class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-sm font-bold text-slate-600 flex-shrink-0">
                            {{ $queue->queue_number }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $queue->queue_code }}</p>
                            <p class="text-xs text-slate-500">
                                {{ $queue->department->name }} · {{ $queue->queue_date->format('M j, Y') }}
                            </p>
                        </div>
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

    {{-- Medical History --}}
    @if($patient->medical_history)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <h3 class="font-semibold text-slate-800 text-sm mb-3">Medical History</h3>
        <p class="text-sm text-slate-600 leading-relaxed">{{ $patient->medical_history }}</p>
    </div>
    @endif

</div>

@endsection
