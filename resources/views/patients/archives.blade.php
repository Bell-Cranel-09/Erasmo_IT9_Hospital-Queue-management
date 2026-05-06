@extends('layouts.app')

@section('title', 'Patient Archives – St. Gabriel Medical Center')
@section('page-title', 'Patient Archives')

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-slate-500">
        Permanently archived patient records with full data snapshots.
    </p>
    <a href="{{ route('patients.trashed') }}"
       class="text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-xl transition">
        ← Deleted Patients
    </a>
</div>

<div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-3.5 mb-5 flex items-center gap-3">
    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="text-sm text-red-700">
        These records are <strong>permanently archived</strong> — the original accounts no longer exist.
        A full data snapshot is preserved here for audit purposes.
    </p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

    @if($archives->isEmpty())
    <div class="text-center py-16">
        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
            </svg>
        </div>
        <p class="text-slate-500 font-medium">No archived patients</p>
        <p class="text-slate-400 text-sm mt-1">Archived records will appear here</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Patient</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Code</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Email</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Appts</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Queues</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Archived By</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Archived At</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($archives as $archive)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-orange-200 flex items-center justify-center text-orange-700 text-sm font-bold flex-shrink-0">
                                {{ strtoupper(substr($archive->first_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-600">{{ $archive->full_name }}</p>
                                <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full">
                                    Archived
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-xs font-mono text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">
                            {{ $archive->patient_code }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-sm text-slate-500">{{ $archive->email }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-sm font-semibold text-slate-700">{{ $archive->total_appointments }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-sm font-semibold text-slate-700">{{ $archive->total_queues }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-sm text-slate-500">{{ $archive->deleted_by_name ?? '—' }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-sm text-orange-600">
                            {{ $archive->archived_at->format('M j, Y h:i A') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($archives->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">
        {{ $archives->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
