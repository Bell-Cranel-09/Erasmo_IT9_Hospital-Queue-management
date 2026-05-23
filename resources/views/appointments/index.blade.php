@extends('layouts.app')

@section('title', 'Appointments – St. Gabriel Medical Center')
@section('page-title', 'Appointments')

@section('content')

{{-- Header row --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <p class="text-sm text-slate-500">Manage and track all patient appointments</p>
    </div>
    <a href="{{ route('appointments.create') }}"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-lg shadow-blue-500/25 hover:-translate-y-0.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Book Appointment
    </a>
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 mb-4">
    <form method="GET" action="{{ route('appointments.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Status</label>
            <select name="status"
                    onchange="this.form.submit()"
                    class="px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white min-w-[130px]">
                <option value="">All Statuses</option>
                <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="canceled"  {{ request('status') == 'canceled'  ? 'selected' : '' }}>Canceled</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Date</label>
            <input type="date" name="date" value="{{ request('date') }}"
                   onchange="this.form.submit()"
                   class="px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex gap-2">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition-all">
                Filter
            </button>
            <a href="{{ route('appointments.index') }}"
               class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium px-4 py-2 rounded-xl transition-all">
                Reset
            </a>
        </div>
    </form>
</div>

{{-- Appointments table --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    @if($appointments->isEmpty())
    <div class="text-center py-16">
        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <p class="text-slate-500 font-medium">No appointments found</p>
        <p class="text-slate-400 text-sm mt-1">
            <a href="{{ route('appointments.create') }}" class="text-blue-600 hover:underline">Book the first appointment</a>
        </p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Reference</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Patient</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Doctor</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Date & Time</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Status</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($appointments as $appt)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <span class="text-xs font-mono font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">
                            {{ $appt->reference_code }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($appt->patient->first_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $appt->patient->full_name }}</p>
                                <p class="text-xs text-slate-400">{{ $appt->patient->patient_code }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <p class="text-sm text-slate-800">{{ $appt->doctor->full_name }}</p>
                        <p class="text-xs text-slate-400">{{ $appt->doctor->department->name }}</p>
                    </td>
                    <td class="px-5 py-3.5">
                        <p class="text-sm font-medium text-slate-800">{{ $appt->appointment_date->format('M j, Y') }}</p>
                        <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}</p>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="badge-{{ $appt->status }} text-xs font-semibold px-2.5 py-1 rounded-full capitalize">
                            {{ $appt->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('appointments.show', $appt) }}"
                               class="text-xs font-medium text-blue-600 hover:text-blue-700 px-2 py-1 rounded-lg hover:bg-blue-50 transition">
                                View
                            </a>
                            @if($appt->isPending() && (auth()->user()->isAdmin() || auth()->user()->isStaff()))
                            <form method="POST" action="{{ route('appointments.confirm', $appt) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs font-medium text-green-600 hover:text-green-700 px-2 py-1 rounded-lg hover:bg-green-50 transition">
                                    Confirm
                                </button>
                            </form>
                            @endif
                            @if(!$appt->isCompleted() && !$appt->isCanceled())
                            <form method="POST" action="{{ route('appointments.cancel', $appt) }}" class="inline"
                                  onsubmit="return confirm('Cancel this appointment?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-600 px-2 py-1 rounded-lg hover:bg-red-50 transition">
                                    Cancel
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($appointments->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">
        {{ $appointments->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
