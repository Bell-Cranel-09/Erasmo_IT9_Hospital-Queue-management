@extends('layouts.app')

@section('title', 'Patients – St. Gabriel Medical Center')
@section('page-title', 'Patients')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <p class="text-sm text-slate-500">All registered patients in the system</p>
    <div class="flex items-center gap-3">
        {{-- Archives link (admin only) --}}
        @if(auth()->user()->isAdmin())
       {{-- CORRECT --}}
        <a href="{{ route('patients.trashed') }}"
        class="inline-flex items-center gap-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-xl transition border border-red-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Deleted Patients
        </a>
        @endif
        <a href="{{ route('patients.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-lg shadow-blue-500/25 hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Register Patient
        </a>
    </div>
</div>

{{-- Search --}}
<div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 mb-4">
    <form method="GET" action="{{ route('patients.index') }}" class="flex gap-3">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input name="search" type="text" value="{{ request('search') }}"
                   placeholder="Search by name or patient code..."
                   class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
            Search
        </button>
        @if(request('search'))
        <a href="{{ route('patients.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium px-4 py-2 rounded-xl transition flex items-center">
            Clear
        </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

    @if($patients->isEmpty())
    <div class="text-center py-16">
        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <p class="text-slate-500 font-medium">No patients found</p>
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
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Registered</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($patients as $patient)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $patient->full_name }}</p>
                                <p class="text-xs text-slate-400">{{ $patient->user?->email ?? '—' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-xs font-mono font-semibold text-slate-600 bg-slate-100 px-2 py-1 rounded-lg">
                            {{ $patient->patient_code }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-sm text-slate-600 capitalize">{{ $patient->gender }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-sm text-slate-600">{{ $patient->phone ?? '—' }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-sm text-slate-500">{{ $patient->created_at->format('M j, Y') }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('patients.show', $patient) }}"
                               class="text-xs font-medium text-blue-600 hover:text-blue-700 px-2 py-1.5 rounded-lg hover:bg-blue-50 transition">
                                View
                            </a>
                            <a href="{{ route('patients.edit', $patient) }}"
                               class="text-xs font-medium text-slate-600 hover:text-slate-700 px-2 py-1.5 rounded-lg hover:bg-slate-100 transition">
                                Edit
                            </a>
                            @if(auth()->user()->isAdmin())
                            {{-- Trigger delete modal --}}
                            <button type="button"
                                    onclick="openDeleteModal({{ $patient->id }}, '{{ addslashes($patient->full_name) }}')"
                                    class="text-xs font-medium text-red-500 hover:text-red-600 px-2 py-1.5 rounded-lg hover:bg-red-50 transition">
                                Delete
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($patients->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">{{ $patients->links() }}</div>
    @endif
    @endif
</div>

{{-- ── DELETE CONFIRMATION MODAL ────────────────────────────────────── --}}
@if(auth()->user()->isAdmin())
<div id="delete-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md animate-slide-up">
        <div class="p-6">
            {{-- Icon --}}
            <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="text-lg font-bold text-slate-800 text-center mb-1">Delete Patient Account</h3>
            <p class="text-sm text-slate-500 text-center mb-1">
                You are about to permanently delete:
            </p>
            <p id="modal-patient-name" class="text-base font-bold text-red-600 text-center mb-4"></p>

            {{-- Warning box --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-4">
                <p class="text-xs text-amber-700 flex items-start gap-2">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    The account will be permanently deleted. All data (appointments, queue history, profile) will be saved to the <strong>Archives</strong> before deletion.
                </p>
            </div>

            {{-- Reason input --}}
            <form id="delete-form" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Reason for deletion <span class="text-red-400">*</span>
                    </label>
                    <textarea name="reason" rows="2" required
                              placeholder="e.g. Patient requested account removal, Duplicate record, etc."
                              class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-400 resize-none"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                            class="flex-1 py-2.5 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-all shadow-lg shadow-red-500/25">
                        Yes, Delete & Archive
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
function openDeleteModal(patientId, patientName) {
    document.getElementById('modal-patient-name').textContent = patientName;
    document.getElementById('delete-form').action = '/patients/' + patientId;
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    document.getElementById('delete-form').reset();
}

// Close modal when clicking outside
document.getElementById('delete-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush

@endsection
