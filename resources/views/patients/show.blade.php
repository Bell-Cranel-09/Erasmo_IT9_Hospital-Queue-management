@extends('layouts.app')

@section('title', 'Patient Profile – St. Gabriel Medical Center')
@section('page-title', 'Patient Profile')

@section('content')

<div class="max-w-4xl mx-auto space-y-5">

    {{-- ── PROFILE HEADER CARD ─────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

        {{-- Blue banner --}}
        <div class="bg-gradient-to-r from-blue-700 to-blue-500 h-24 relative">
            {{-- Admin-only delete button --}}
            @if(auth()->user()->isAdmin())
            <div class="absolute top-4 right-4">
                <form method="POST" action="{{ route('patients.destroy', $patient) }}"
                      onsubmit="return confirm('Deactivate {{ $patient->full_name }}? Their data will be preserved.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="flex items-center gap-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-3 py-1.5 rounded-xl transition shadow-lg">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Deactivate
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- Avatar + name row --}}
        <div class="px-6 pb-6">
            <div class="flex items-end gap-4 -mt-10 mb-5">

                {{-- Avatar circle --}}
                <div class="w-20 h-20 rounded-2xl bg-white shadow-xl border-4 border-white flex items-center justify-center flex-shrink-0">
                    <span class="text-3xl font-bold text-blue-600">
                        {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                    </span>
                </div>

                {{-- Name + code --}}
                <div class="pb-1 flex-1 min-w-0">
                    <h2 class="text-xl font-bold text-slate-800">{{ $patient->full_name }}</h2>
                    <p class="text-sm text-slate-500">{{ $patient->patient_code }}</p>
                </div>

                {{-- Action buttons --}}
                <div class="pb-1 flex gap-2 flex-shrink-0">
                    <a href="{{ route('patients.edit', $patient) }}"
                       class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 px-3 py-2 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                    <a href="{{ route('patients.index') }}"
                       class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-xl transition">
                        ← Back
                    </a>
                    @endif
                </div>
            </div>

            {{-- Info grid — matches screenshot layout --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-5 pt-4 border-t border-slate-100">
                <div>
                    <p class="text-xs text-slate-400 mb-1">Date of Birth</p>
                    <p class="text-sm font-semibold text-slate-700">
                        {{ $patient->date_of_birth->format('M j, Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Gender</p>
                    <p class="text-sm font-semibold text-slate-700 capitalize">{{ $patient->gender }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Phone</p>
                    <p class="text-sm font-semibold text-slate-700">{{ $patient->phone ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Email</p>
                    <p class="text-sm font-semibold text-slate-700 truncate">{{ $patient->user->email }}</p>
                </div>
            </div>

            @if($patient->address)
            <div class="mt-4 pt-3 border-t border-slate-100">
                <p class="text-xs text-slate-400 mb-1">Address</p>
                <p class="text-sm text-slate-700">{{ $patient->address }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ── ACTIVE QUEUE BADGE ───────────────────────────────────────── --}}
    @php $activeQueue = $patient->activeQueue(); @endphp
    @if($activeQueue)
    <div class="bg-blue-50 border border-blue-200 rounded-2xl px-5 py-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-xl flex-shrink-0">
            {{ $activeQueue->queue_number }}
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-blue-800">Currently in Queue</p>
            <p class="text-xs text-blue-600 mt-0.5">
                {{ $activeQueue->queue_code }} · {{ $activeQueue->department->name }} ·
                Status: <span class="font-semibold capitalize">{{ $activeQueue->status }}</span>
            </p>
        </div>
    </div>
    @endif

    {{-- ── APPOINTMENTS + QUEUE HISTORY ────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Appointments --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800 text-sm">Appointments</h3>
                <a href="{{ route('appointments.create') }}"
                   class="text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg transition">
                    + Book
                </a>
            </div>

            @if($patient->appointments->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-400">No appointments yet</p>
            </div>
            @else
            <div class="divide-y divide-slate-50">
                @foreach($patient->appointments->take(5) as $appt)
                <div class="px-5 py-3 flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 truncate">{{ $appt->doctor->full_name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $appt->appointment_date->format('M j, Y') }} ·
                            {{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="badge-{{ $appt->status }} text-xs font-semibold px-2.5 py-1 rounded-full capitalize">
                            {{ $appt->status }}
                        </span>
                        @if(!$appt->isCompleted() && !$appt->isCanceled())
                        <form method="POST" action="{{ route('appointments.cancel', $appt) }}"
                              onsubmit="return confirm('Cancel this appointment?')">
                            @csrf @method('PATCH')
                            <button type="submit" title="Cancel"
                                    class="w-6 h-6 rounded-lg flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @if($patient->appointments->count() > 5)
            <div class="px-5 py-3 border-t border-slate-100 text-center">
                <a href="{{ route('appointments.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
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
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-400">No queue history yet</p>
            </div>
            @else
            <div class="divide-y divide-slate-50">
                @foreach($patient->queues->take(5) as $queue)
                <div class="px-5 py-3 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-sm font-bold text-slate-600 flex-shrink-0">
                        {{ $queue->queue_number }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800">{{ $queue->queue_code }}</p>
                        <p class="text-xs text-slate-500">
                            {{ $queue->department->name }} · {{ $queue->queue_date->format('M j, Y') }}
                        </p>
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

    {{-- ── MEDICAL HISTORY ─────────────────────────────────────────── --}}
    @if($patient->medical_history || auth()->user()->isAdmin() || auth()->user()->isStaff())
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-slate-800 text-sm">Medical History</h3>
            <a href="{{ route('patients.edit', $patient) }}"
               class="text-xs text-blue-600 hover:text-blue-700 font-medium">Edit</a>
        </div>
        @if($patient->medical_history)
            <p class="text-sm text-slate-600 leading-relaxed">{{ $patient->medical_history }}</p>
        @else
            <p class="text-sm text-slate-400 italic">No medical history recorded yet.</p>
        @endif
    </div>
    @endif

</div>

@endsection
