@extends('layouts.app')

@section('title', 'Archived Record – St. Gabriel Medical Center')
@section('page-title', 'Archived Patient Record')

@section('content')

<div class="max-w-4xl mx-auto space-y-5">

    {{-- Header card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-600 to-slate-700 h-16 relative">
            <div class="absolute top-4 right-4">
                <span class="flex items-center gap-1.5 bg-orange-500 text-white text-xs font-bold px-3 py-1.5 rounded-xl">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    Archived Record
                </span>
            </div>
        </div>

        <div class="px-6 py-5">
            <div class="flex flex-wrap items-center gap-4 mb-5">
                <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg"
                     style="flex-shrink:0; border-radius:12px; display:block;">
                    <rect width="64" height="64" fill="#64748b" rx="12"/>
                    <text x="32" y="32" dominant-baseline="central" text-anchor="middle"
                          fill="white" font-size="28" font-weight="bold"
                          font-family="Impact, Arial Black, sans-serif">
                        {{ strtoupper(mb_substr($archive->first_name, 0, 1)) }}
                    </text>
                </svg>
                <div class="flex-1" style="min-width:0;">
                    <h2 class="text-lg font-bold text-slate-700" style="word-break:break-word;">
                        {{ $archive->full_name }}
                    </h2>
                    <p class="text-sm text-slate-400 mt-0.5">{{ $archive->patient_code }} · Account deleted</p>
                </div>
                <a href="{{ route('patients.archives') }}"
                   class="text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-xl transition flex-shrink-0">
                    ← Back to Archives
                </a>
            </div>

            {{-- Patient info grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-5 pt-4 border-t border-slate-100">
                <div>
                    <p class="text-xs text-slate-400 mb-1">Date of Birth</p>
                    <p class="text-sm font-semibold text-slate-600">{{ $archive->date_of_birth->format('M j, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Gender</p>
                    <p class="text-sm font-semibold text-slate-600 capitalize">{{ $archive->gender }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Phone</p>
                    <p class="text-sm font-semibold text-slate-600">{{ $archive->phone ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Email</p>
                    <p class="text-sm font-semibold text-slate-600" style="word-break:break-all;">{{ $archive->email }}</p>
                </div>
            </div>

            @if($archive->address)
            <div class="mt-4 pt-3 border-t border-slate-100">
                <p class="text-xs text-slate-400 mb-1">Address</p>
                <p class="text-sm text-slate-600">{{ $archive->address }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Deletion info --}}
    <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-red-700 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Deletion Details
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-red-400 mb-1">Deleted By</p>
                <p class="text-sm font-semibold text-red-700">{{ $archive->deleted_by_name ?? 'Admin' }}</p>
            </div>
            <div>
                <p class="text-xs text-red-400 mb-1">Deleted At</p>
                <p class="text-sm font-semibold text-red-700">{{ $archive->archived_at->format('M j, Y h:i A') }}</p>
            </div>
            <div>
                <p class="text-xs text-red-400 mb-1">Reason</p>
                <p class="text-sm font-semibold text-red-700">{{ $archive->deletion_reason ?? 'Deleted by admin' }}</p>
            </div>
        </div>
    </div>

    {{-- Appointment history snapshot --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800 text-sm">Appointment History Snapshot</h3>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                {{ $archive->total_appointments }} total
            </span>
        </div>

        @if(empty($archive->appointments_snapshot))
        <div class="text-center py-10">
            <p class="text-sm text-slate-400">No appointment history</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-2.5">Reference</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-2.5">Doctor</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-2.5">Date</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-2.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($archive->appointments_snapshot as $appt)
                    <tr>
                        <td class="px-5 py-3">
                            <span class="text-xs font-mono text-blue-600 bg-blue-50 px-2 py-0.5 rounded">
                                {{ $appt['reference_code'] ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-sm text-slate-600">{{ $appt['doctor'] ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-slate-600">{{ $appt['appointment_date'] ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="badge-{{ $appt['status'] ?? 'pending' }} text-xs font-semibold px-2.5 py-1 rounded-full capitalize">
                                {{ $appt['status'] ?? '—' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Queue history snapshot --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800 text-sm">Queue History Snapshot</h3>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                {{ $archive->total_queues }} total
            </span>
        </div>

        @if(empty($archive->queues_snapshot))
        <div class="text-center py-10">
            <p class="text-sm text-slate-400">No queue history</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-2.5">Queue</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-2.5">Department</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-2.5">Date</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-2.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($archive->queues_snapshot as $queue)
                    <tr>
                        <td class="px-5 py-3">
                            <span class="text-xs font-mono text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                {{ $queue['queue_code'] ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-sm text-slate-600">{{ $queue['department'] ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-slate-600">{{ $queue['queue_date'] ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="badge-{{ $queue['status'] ?? 'done' }} text-xs font-semibold px-2.5 py-1 rounded-full capitalize">
                                {{ $queue['status'] ?? '—' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Medical history snapshot --}}
    @if($archive->medical_history)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <h3 class="font-semibold text-slate-800 text-sm mb-3">Medical History</h3>
        <p class="text-sm text-slate-600 leading-relaxed">{{ $archive->medical_history }}</p>
    </div>
    @endif

</div>

@endsection
