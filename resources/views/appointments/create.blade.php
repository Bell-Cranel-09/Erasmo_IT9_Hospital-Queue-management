@extends('layouts.app')

@section('title', 'Book Appointment – St. Gabriel Medical Center')
@section('page-title', 'Book Appointment')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5">
            <h2 class="text-white font-semibold text-lg">New Appointment</h2>
            <p class="text-blue-200 text-sm mt-0.5">Fill in the details to schedule a visit</p>
        </div>

        <form method="POST" action="{{ route('appointments.store') }}" class="p-6 space-y-5" id="booking-form">
            @csrf

            {{-- Validation errors --}}
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                <p class="text-sm font-medium text-red-700 mb-1">Please fix the following:</p>
                <ul class="text-sm text-red-600 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li class="flex items-center gap-1">
                            <span class="w-1 h-1 rounded-full bg-red-400 flex-shrink-0"></span>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Patient (staff/admin see dropdown, patients see themselves) --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Patient <span class="text-red-400">*</span>
                </label>
                <select name="patient_id" required
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white @error('patient_id') border-red-300 @enderror">
                    <option value="">Select a patient...</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->full_name }} ({{ $patient->patient_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            @else
            {{-- Patient is logged in; pass their ID automatically --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500">Booking as yourself</p>
                </div>
            </div>
            @endif

            {{-- Doctor --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Doctor <span class="text-red-400">*</span>
                </label>
                <select name="doctor_id" id="doctor-select" required
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white @error('doctor_id') border-red-300 @enderror"
                        onchange="loadSlots()">
                    <option value="">Select a doctor...</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                            {{ $doctor->full_name }} – {{ $doctor->department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Appointment Date <span class="text-red-400">*</span>
                </label>
                <input type="date" name="date" id="date-input"
                       value="{{ old('date') }}"
                       min="{{ now()->addDay()->format('Y-m-d') }}"
                       required
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('date') border-red-300 @enderror"
                       onchange="loadSlots()">
                <p class="text-xs text-slate-400 mt-1">Select a date to see available time slots</p>
            </div>

            {{-- Time Slots (dynamic) --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Time Slot <span class="text-red-400">*</span>
                </label>
                <div id="slots-container">
                    <p class="text-sm text-slate-400 italic">Select a doctor and date first to see available slots.</p>
                </div>
                <input type="hidden" name="time" id="time-hidden" value="{{ old('time') }}">
            </div>

            {{-- Reason --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Reason for Visit <span class="text-slate-400 font-normal">(optional)</span>
                </label>
                <textarea name="reason" rows="3" placeholder="Brief description of symptoms or reason..."
                          class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('reason') }}</textarea>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('appointments.index') }}"
                   class="flex-1 text-center py-2.5 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 rounded-xl text-sm transition-all shadow-lg shadow-blue-500/25 hover:-translate-y-0.5">
                    Book Appointment
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let selectedSlot = null;

    async function loadSlots() {
        const doctorId = document.getElementById('doctor-select').value;
        const date     = document.getElementById('date-input').value;
        const container = document.getElementById('slots-container');

        // Reset selection
        selectedSlot = null;
        document.getElementById('time-hidden').value = '';

        if (!doctorId || !date) {
            container.innerHTML = '<p class="text-sm text-slate-400 italic">Select a doctor and date first.</p>';
            return;
        }

        // Loading state
        container.innerHTML = `
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <svg class="animate-spin w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Loading available slots...
            </div>`;

        try {
            const res  = await fetch(`/appointments/slots?doctor_id=${doctorId}&date=${date}`);
            const data = await res.json();

            if (!data.slots || data.slots.length === 0) {
                container.innerHTML = `
                    <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-700">
                        No available slots for this doctor on this date. Try a different date.
                    </div>`;
                return;
            }

            // Render slot buttons
            const grid = document.createElement('div');
            grid.className = 'grid grid-cols-3 sm:grid-cols-4 gap-2';

            data.slots.forEach(slot => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = slot;
                btn.dataset.slot = slot;
                btn.className = 'slot-btn py-2 px-3 border border-slate-200 rounded-xl text-sm text-slate-700 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 transition-all font-medium';
                btn.onclick = () => selectSlot(slot, btn);
                grid.appendChild(btn);
            });

            container.innerHTML = '';
            container.appendChild(grid);

            // Re-select if user had previously selected (after validation error)
            const oldTime = '{{ old('time') }}';
            if (oldTime) {
                const oldBtn = grid.querySelector(`[data-slot="${oldTime}"]`);
                if (oldBtn) selectSlot(oldTime, oldBtn);
            }
        } catch (e) {
            container.innerHTML = '<p class="text-sm text-red-500">Failed to load slots. Please try again.</p>';
        }
    }

    function selectSlot(slot, btn) {
        // Reset all buttons
        document.querySelectorAll('.slot-btn').forEach(b => {
            b.className = 'slot-btn py-2 px-3 border border-slate-200 rounded-xl text-sm text-slate-700 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 transition-all font-medium';
        });
        // Highlight selected
        btn.className = 'slot-btn py-2 px-3 border-2 border-blue-600 rounded-xl text-sm text-blue-700 bg-blue-50 font-bold transition-all';

        selectedSlot = slot;
        document.getElementById('time-hidden').value = slot;
    }

    // Load slots on page load if values exist (after validation error)
    window.addEventListener('DOMContentLoaded', () => {
        const doctor = document.getElementById('doctor-select').value;
        const date   = document.getElementById('date-input').value;
        if (doctor && date) loadSlots();
    });
</script>
@endpush

@endsection
