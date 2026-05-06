@extends('layouts.app')

@section('title', 'Deleted Patients – St. Gabriel Medical Center')
@section('page-title', 'Deleted Patients')

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-slate-500">
        Soft-deleted patients. Restore to make active again, or Archive to permanently save the record.
    </p>
    <div class="flex items-center gap-3">
        <a href="{{ route('patients.archives') }}"
           class="text-sm font-medium text-orange-600 bg-orange-50 hover:bg-orange-100 px-3 py-2 rounded-xl transition border border-orange-200 inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
            </svg>
            View Archives
        </a>
        <a href="{{ route('patients.index') }}"
           class="text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-xl transition">
            ← Active Patients
        </a>
    </div>
</div>

{{-- Info banner --}}
<div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-3.5 mb-5 flex items-center gap-3">
    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div class="text-sm text-amber-700">
        <strong>Restore</strong> — brings the patient back as active. &nbsp;|&nbsp;
        <strong>Archive</strong> — saves a permanent snapshot then permanently removes the record.
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

    @if($patients->isEmpty())
    <div class="text-center py-16">
        <div class="w-16 h-16 rounded-2xl bg-green-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-slate-500 font-medium">No deleted patients</p>
        <p class="text-slate-400 text-sm mt-1">All patient accounts are currently active</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Patient</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Code</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Gender</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Phone</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Deleted At</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($patients as $patient)
                <tr class="hover:bg-slate-50 transition-colors bg-red-50/20">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-slate-300 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-500 line-through">
                                    {{ $patient->full_name }}
                                </p>
                                <p class="text-xs text-slate-400">{{ $patient->user?->email ?? '—' }}</p>
                                <span class="text-xs font-medium text-red-500 bg-red-50 px-2 py-0.5 rounded-full">
                                    Deleted
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-xs font-mono text-slate-400 bg-slate-100 px-2 py-1 rounded-lg">
                            {{ $patient->patient_code }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-sm text-slate-400 capitalize">{{ $patient->gender }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-sm text-slate-400">{{ $patient->phone ?? '—' }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-sm text-red-500">
                            {{ $patient->deleted_at->format('M j, Y h:i A') }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">

                            {{-- Restore button --}}
                            <form method="POST" action="{{ route('patients.restore', $patient->id) }}"
                                  onsubmit="return confirm('Restore {{ addslashes($patient->full_name) }}? Their account will become active again.')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 px-3 py-1.5 rounded-xl transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Restore
                                </button>
                            </form>

                            {{-- Archive button --}}
                            <form method="POST" action="{{ route('patients.archive', $patient->id) }}"
                                  onsubmit="return confirm('Archive {{ addslashes($patient->full_name) }}?\n\nThis will:\n• Save a full data snapshot to Archives\n• Permanently remove the record\n\nThis cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-orange-600 bg-orange-50 hover:bg-orange-100 border border-orange-200 px-3 py-1.5 rounded-xl transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                    </svg>
                                    Archive
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($patients->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">
        {{ $patients->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
