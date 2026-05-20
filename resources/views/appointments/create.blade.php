@extends('layouts.app')

@section('title', 'Book Appointment – St. Gabriel Medical Center')
@section('page-title', 'Book Appointment')

@section('content')

<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-slate-500">Fill in the details below to schedule an appointment.</p>
        <a href="{{ route('appointments.index') }}"
           class="text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-xl transition">
            ← My Appointments
        </a>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm mb-5 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

        <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-5">
            <h2 class="text-white font-semibold text-lg">New Appointment</h2>
            <p class="text-blue-100 text-sm mt-0.5">St. Gabriel Medical Center</p>
        </div>

        <form method="POST" action="{{ route('appointments.store') }}" class="p-6 space-y-6" id="appointment-form">
            @csrf

            {{-- Admin/Staff: Select Patient --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Patient <span class="text-red-400">*</span>
                </label>
                <select name="patient_id" required
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">Select patient...</option>
                    @foreach($patients as $pat)
                        <option value="{{ $pat->id }}" {{ old('patient_id') == $pat->id ? 'selected' : '' }}>
                            {{ $pat->full_name }} — {{ $pat->patient_code }}
                        </option>
                    @endforeach
                </select>
                @error('patient_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endif

            {{-- Step 1: Department --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    <span class="inline-flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">1</span>
                        Select Department <span class="text-red-400">*</span>
                    </span>
                </label>
                <select name="department_id" id="department-select" required
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">Choose a department...</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Step 2: Doctor --}}
            <div id="doctor-section" class="{{ old('department_id') ? '' : 'opacity-50 pointer-events-none' }}">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    <span class="inline-flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">2</span>
                        Select Doctor <span class="text-red-400">*</span>
                    </span>
                </label>
                <select name="doctor_id" id="doctor-select" required
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">Select a department first...</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ (old('doctor_id') == $doc->id || $selectedDoctorId == $doc->id) ? 'selected' : '' }}>
                            {{ $doc->full_name }} — {{ $doc->specialization }}
                        </option>
                    @endforeach
                </select>
                @error('doctor_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Step 3: Date --}}
            <div id="date-section" class="{{ old('doctor_id') ? '' : 'opacity-50 pointer-events-none' }}">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    <span class="inline-flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">3</span>
                        Select Date <span class="text-red-400">*</span>
                    </span>
                </label>
                <input type="date" name="appointment_date" id="date-input"
                       value="{{ old('appointment_date') }}"
                       min="{{ now()->format('Y-m-d') }}"
                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                       required>
                @error('appointment_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Step 4: Time Slot --}}
            <div id="slot-section" class="opacity-50 pointer-events-none">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    <span class="inline-flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">4</span>
                        Select Time Slot <span class="text-red-400">*</span>
                    </span>
                </label>

                {{-- Loading indicator --}}
                <div id="slots-loading" class="hidden flex items-center gap-2 text-sm text-slate-500 py-3">
                    <svg class="w-4 h-4 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Loading available slots...
                </div>

                {{-- Slot grid --}}
                <div id="slots-grid" class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                    <p class="text-sm text-slate-400 col-span-full italic">Select a doctor and date first.</p>
                </div>

                {{-- Hidden input that stores selected time --}}
                <input type="hidden" name="appointment_time" id="selected-time" value="{{ old('appointment_time') }}">
                @error('appointment_time')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Reason (optional) --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Reason for Visit <span class="text-slate-400 font-normal">(optional)</span>
                </label>
                <textarea name="reason" rows="3"
                          placeholder="Briefly describe your symptoms or reason for the visit..."
                          class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('reason') }}</textarea>
            </div>

            {{-- Submit --}}
            <div class="pt-2">
                <button type="submit" id="submit-btn"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 rounded-xl transition-all shadow-lg shadow-blue-500/20 hover:-translate-y-0.5 flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Book Appointment
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

// ── Department → load doctors ──────────────────────────────────────────────
document.getElementById('department-select')?.addEventListener('change', async function () {
    const deptId      = this.value;
    const doctorSel   = document.getElementById('doctor-select');
    const doctorSec   = document.getElementById('doctor-section');
    const dateSection = document.getElementById('date-section');
    const slotSection = document.getElementById('slot-section');

    // Reset downstream
    doctorSel.innerHTML = '<option value="">Loading doctors...</option>';
    document.getElementById('slots-grid').innerHTML = '<p class="text-sm text-slate-400 col-span-full italic">Select a doctor and date first.</p>';
    document.getElementById('selected-time').value  = '';
    dateSection.classList.add('opacity-50', 'pointer-events-none');
    slotSection.classList.add('opacity-50', 'pointer-events-none');

    if (!deptId) {
        doctorSel.innerHTML = '<option value="">Select a department first...</option>';
        doctorSec.classList.add('opacity-50', 'pointer-events-none');
        return;
    }

    doctorSec.classList.remove('opacity-50', 'pointer-events-none');

    try {
        const res  = await fetch(`/doctors?department_id=${deptId}&json=1`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });

        // Fallback: just fetch doctors for this dept via a simple endpoint
        const res2 = await fetch(`/appointments/doctors-by-dept?department_id=${deptId}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });

        if (res2.ok) {
            const data = await res2.json();
            if (data.doctors && data.doctors.length > 0) {
                doctorSel.innerHTML = '<option value="">Choose a doctor...</option>' +
                    data.doctors.map(d => `<option value="${d.id}">${d.full_name} — ${d.specialization}</option>`).join('');
            } else {
                doctorSel.innerHTML = '<option value="">No doctors available in this department</option>';
            }
        }
    } catch (e) {
        doctorSel.innerHTML = '<option value="">Could not load doctors. Try refreshing.</option>';
    }
});

// ── Doctor or Date changes → load slots ───────────────────────────────────
function tryLoadSlots() {
    const doctorId = document.getElementById('doctor-select')?.value;
    const date     = document.getElementById('date-input')?.value;
    const dateSection = document.getElementById('date-section');
    const slotSection = document.getElementById('slot-section');

    if (doctorId) {
        dateSection.classList.remove('opacity-50', 'pointer-events-none');
    }

    if (!doctorId || !date) return;

    slotSection.classList.remove('opacity-50', 'pointer-events-none');
    loadSlots(doctorId, date);
}

document.getElementById('doctor-select')?.addEventListener('change', tryLoadSlots);
document.getElementById('date-input')?.addEventListener('change',    tryLoadSlots);

async function loadSlots(doctorId, date) {
    const grid    = document.getElementById('slots-grid');
    const loading = document.getElementById('slots-loading');

    loading.classList.remove('hidden');
    grid.innerHTML = '';
    document.getElementById('selected-time').value = '';

    try {
        const res  = await fetch(`/appointments/slots?doctor_id=${doctorId}&date=${date}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        loading.classList.add('hidden');

        if (!data.slots || data.slots.length === 0) {
            grid.innerHTML = `<p class="text-sm text-slate-400 col-span-full italic">${data.message || 'No slots available for this day.'}</p>`;
            return;
        }

        grid.innerHTML = data.slots.map(slot => `
            <button type="button"
                    onclick="selectSlot('${slot.time}', '${slot.label}', this)"
                    ${!slot.available ? 'disabled' : ''}
                    class="slot-btn py-2.5 px-3 rounded-xl border text-sm font-medium transition-all
                           ${slot.available
                               ? 'border-slate-200 text-slate-700 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 cursor-pointer'
                               : 'border-slate-100 text-slate-300 bg-slate-50 cursor-not-allowed line-through'
                           }">
                ${slot.label}
            </button>
        `).join('');

        // Re-select previously chosen time if any (for validation errors)
        const prev = '{{ old("appointment_time") }}';
        if (prev) {
            document.querySelectorAll('.slot-btn').forEach(btn => {
                if (btn.dataset?.time === prev || btn.onclick?.toString().includes(prev)) {
                    btn.click();
                }
            });
        }

    } catch (e) {
        loading.classList.add('hidden');
        grid.innerHTML = '<p class="text-sm text-red-400 col-span-full">Could not load slots. Please try again.</p>';
    }
}

function selectSlot(time, label, btn) {
    // Clear previous selection
    document.querySelectorAll('.slot-btn').forEach(b => {
        b.classList.remove('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-md');
        b.classList.add('border-slate-200', 'text-slate-700');
    });

    // Highlight selected
    btn.classList.remove('border-slate-200', 'text-slate-700');
    btn.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-md');

    document.getElementById('selected-time').value = time;
}
</script>
@endpush

@endsection
