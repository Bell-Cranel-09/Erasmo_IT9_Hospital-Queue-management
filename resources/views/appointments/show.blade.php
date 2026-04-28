@extends('layouts.app')

@section('title', 'Appointment – St. Gabriel Medical Center')
@section('page-title', 'Appointment Detail')

@section('content')

<div class="max-w-2xl mx-auto space-y-4">

    {{-- Status banner --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs text-slate-500 mb-1">Reference Code</p>
                <p class="text-xl font-mono font-bold text-blue-700">{{ $appointment->reference_code }}</p>
            </div>
            <span class="badge-{{ $appointment->status }} text-sm font-semibold px-4 py-1.5 rounded-full capitalize flex-shrink-0">
                {{ $appointment->status }}
            </span>
        </div>
    </div>

    {{-- Details card --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-4">
        <h3 class="font-semibold text-slate-800 text-base border-b border-slate-100 pb-3">Appointment Details</h3>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-slate-500 mb-1">Patient</p>
                <p class="text-sm font-semibold text-slate-800">{{ $appointment->patient->full_name }}</p>
                <p class="text-xs text-slate-400">{{ $appointment->patient->patient_code }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-1">Doctor</p>
                <p class="text-sm font-semibold text-slate-800">{{ $appointment->doctor->full_name }}</p>
                <p class="text-xs text-slate-400">{{ $appointment->doctor->department->name }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-1">Date</p>
                <p class="text-sm font-semibold text-slate-800">{{ $appointment->appointment_date->format('F j, Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-1">Time</p>
                <p class="text-sm font-semibold text-slate-800">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</p>
            </div>
        </div>

        @if($appointment->reason)
        <div class="bg-slate-50 rounded-xl p-3">
            <p class="text-xs text-slate-500 mb-1">Reason for Visit</p>
            <p class="text-sm text-slate-700">{{ $appointment->reason }}</p>
        </div>
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('appointments.index') }}"
           class="px-4 py-2.5 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition">
            ← Back to Appointments
        </a>

        @if($appointment->isPending() && (auth()->user()->isAdmin() || auth()->user()->isStaff()))
        <form method="POST" action="{{ route('appointments.confirm', $appointment) }}">
            @csrf @method('PATCH')
            <button type="submit" class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-green-500/25 hover:-translate-y-0.5">
                ✓ Confirm Appointment
            </button>
        </form>
        @endif

        @if(!$appointment->isCompleted() && !$appointment->isCanceled())
        <form method="POST" action="{{ route('appointments.cancel', $appointment) }}"
              onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
            @csrf @method('PATCH')
            <button type="submit" class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold rounded-xl transition border border-red-200">
                ✕ Cancel Appointment
            </button>
        </form>
        @endif
    </div>
</div>

@endsection
