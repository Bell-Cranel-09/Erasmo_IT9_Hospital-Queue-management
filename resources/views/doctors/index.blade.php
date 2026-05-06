@extends('layouts.app')

@section('title', 'Doctors – St. Gabriel Medical Center')
@section('page-title', 'Doctors')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <p class="text-sm text-slate-500">All registered doctors and their schedules</p>
    <div class="flex items-center gap-3">
        {{-- Deleted doctors link (admin only) --}}
        @if(auth()->user()->isAdmin())
        <a href="{{ route('doctors.trashed') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-xl transition border border-red-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Deleted Doctors
        </a>
        <button onclick="document.getElementById('add-doctor-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-lg shadow-blue-500/25 hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Doctor
        </button>
        @endif
    </div>
</div>

{{-- Doctor cards --}}
@if($doctors->isEmpty())
<div class="bg-white rounded-2xl p-16 shadow-sm border border-slate-100 text-center">
    <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <p class="text-slate-500 font-medium">No doctors registered yet</p>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach($doctors as $doctor)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden card-hover">

        {{-- Doctor header --}}
        <div class="bg-gradient-to-br from-blue-600 to-blue-800 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                    {{ strtoupper(substr($doctor->first_name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white font-semibold truncate">{{ $doctor->full_name }}</p>
                    <p class="text-blue-200 text-xs">{{ $doctor->specialization }}</p>
                </div>
            </div>
        </div>

        {{-- Doctor info --}}
        <div class="p-4 space-y-3">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">
                    {{ $doctor->department->name }}
                </span>
                <span class="text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    Active
                </span>
            </div>

            {{-- Schedule preview --}}
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Schedule</p>
                @if($doctor->schedules->where('is_active', true)->isEmpty())
                    <p class="text-xs text-slate-400 italic">No active schedules</p>
                @else
                <div class="space-y-1">
                    @foreach($doctor->schedules->where('is_active', true)->take(3) as $schedule)
                    <div class="flex items-center justify-between text-xs bg-slate-50 rounded-lg px-3 py-1.5">
                        <span class="font-medium text-slate-700">{{ $schedule->day_name }}</span>
                        <span class="text-slate-500">
                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} –
                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Action buttons --}}
            <div class="flex gap-2 pt-1">
                <a href="{{ route('appointments.create', ['doctor_id' => $doctor->id]) }}"
                   class="flex-1 text-center text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-xl transition">
                    Book Appointment
                </a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('doctors.schedules', $doctor) }}"
                   class="text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-xl transition">
                    Schedule
                </a>
                {{-- Delete button --}}
                <form method="POST" action="{{ route('doctors.destroy', $doctor) }}"
                      onsubmit="return confirm('Delete {{ addslashes($doctor->full_name) }}?\n\nThis will:\n• Deactivate all their schedules\n• Cancel upcoming appointments\n• Soft delete the record (data preserved)')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="text-xs font-semibold text-red-500 hover:text-red-600 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-xl transition">
                        Delete
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($doctors->hasPages())
<div class="mt-6">{{ $doctors->links() }}</div>
@endif
@endif

{{-- ── ADD DOCTOR MODAL ─────────────────────────────────────────────── --}}
@if(auth()->user()->isAdmin())
<div id="add-doctor-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto animate-slide-up">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">Add New Doctor</h3>
            <button onclick="document.getElementById('add-doctor-modal').classList.add('hidden')"
                    class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-500 transition">
                ✕
            </button>
        </div>
        <form method="POST" action="{{ route('doctors.store') }}" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">First Name</label>
                    <input name="first_name" type="text" required value="{{ old('first_name') }}"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Last Name</label>
                    <input name="last_name" type="text" required value="{{ old('last_name') }}"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                <input name="email" type="email" required value="{{ old('email') }}"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Department</label>
                <select name="department_id" required
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">Select department...</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Specialization</label>
                <input name="specialization" type="text" required value="{{ old('specialization') }}"
                       placeholder="e.g. General Practitioner"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">License Number</label>
                <input name="license_number" type="text" required value="{{ old('license_number') }}"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
                <input name="phone" type="tel" value="{{ old('phone') }}"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('add-doctor-modal').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-all shadow-lg shadow-blue-500/25">
                    Add Doctor
                </button>
            </div>
        </form>
    </div>
</div>

@if($errors->any())
<script>document.getElementById('add-doctor-modal').classList.remove('hidden');</script>
@endif
@endif

@endsection
