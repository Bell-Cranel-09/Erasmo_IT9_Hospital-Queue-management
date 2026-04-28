@extends('layouts.app')

@section('title', 'Edit Patient – St. Gabriel Medical Center')
@section('page-title', 'Edit Patient')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

        <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-5 flex items-center justify-between">
            <div>
                <h2 class="text-white font-semibold text-lg">Edit Patient Profile</h2>
                <p class="text-slate-300 text-sm mt-0.5">{{ $patient->full_name }} · {{ $patient->patient_code }}</p>
            </div>
            <a href="{{ route('patients.show', $patient) }}"
               class="text-sm text-slate-300 hover:text-white font-medium transition">← Back</a>
        </div>

        <form method="POST" action="{{ route('patients.update', $patient) }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')

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

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">First Name <span class="text-red-400">*</span></label>
                    <input name="first_name" type="text" required value="{{ old('first_name', $patient->first_name) }}"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('first_name') border-red-300 @enderror">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Last Name <span class="text-red-400">*</span></label>
                    <input name="last_name" type="text" required value="{{ old('last_name', $patient->last_name) }}"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('last_name') border-red-300 @enderror">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Date of Birth <span class="text-red-400">*</span></label>
                    <input name="date_of_birth" type="date" required
                           value="{{ old('date_of_birth', $patient->date_of_birth->format('Y-m-d')) }}"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Gender <span class="text-red-400">*</span></label>
                    <select name="gender" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="male"   {{ old('gender', $patient->gender) == 'male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other"  {{ old('gender', $patient->gender) == 'other'  ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
                <input name="phone" type="tel" value="{{ old('phone', $patient->phone) }}"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Address</label>
                <textarea name="address" rows="2"
                          class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none transition">{{ old('address', $patient->address) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Medical History</label>
                <textarea name="medical_history" rows="3" placeholder="Known conditions, allergies, past surgeries..."
                          class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none transition">{{ old('medical_history', $patient->medical_history) }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('patients.show', $patient) }}"
                   class="flex-1 text-center py-2.5 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 rounded-xl text-sm transition-all shadow-lg shadow-blue-500/25 hover:-translate-y-0.5">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
