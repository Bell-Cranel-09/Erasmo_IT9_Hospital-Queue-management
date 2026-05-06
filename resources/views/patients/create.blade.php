@extends('layouts.auth')

@section('title', 'Register Patient – St. Gabriel Medical Center')
@section('page-title', 'Register Patient')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5">
            <h2 class="text-white font-semibold text-lg">New Patient Registration</h2>
            <p class="text-blue-200 text-sm mt-0.5">Fill in the patient's personal information</p>
        </div>

        <form method="POST" action="{{ route('patients.store') }}" class="p-6 space-y-5">
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

            {{-- Section: Personal Info --}}
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-3">Personal Information</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            First Name <span class="text-red-400">*</span>
                        </label>
                        <input name="first_name" type="text" required value="{{ old('first_name') }}"
                               placeholder="Juan"
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('first_name') border-red-300 bg-red-50 @enderror">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Last Name <span class="text-red-400">*</span>
                        </label>
                        <input name="last_name" type="text" required value="{{ old('last_name') }}"
                               placeholder="Dela Cruz"
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('last_name') border-red-300 bg-red-50 @enderror">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Date of Birth <span class="text-red-400">*</span>
                    </label>
                    <input name="date_of_birth" type="date" required value="{{ old('date_of_birth') }}"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('date_of_birth') border-red-300 @enderror">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Gender <span class="text-red-400">*</span>
                    </label>
                    <select name="gender" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white transition @error('gender') border-red-300 @enderror">
                        <option value="">Select gender...</option>
                        <option value="male"   {{ old('gender') == 'male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other"  {{ old('gender') == 'other'  ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Phone Number <span class="text-slate-400 font-normal">(optional)</span>
                </label>
                <input name="phone" type="tel" value="{{ old('phone') }}"
                       placeholder="09171234567"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Address <span class="text-slate-400 font-normal">(optional)</span>
                </label>
                <textarea name="address" rows="2" placeholder="House No., Street, Barangay, City"
                          class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none transition">{{ old('address') }}</textarea>
            </div>

            {{-- Divider --}}
            <div class="border-t border-slate-100 pt-2">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-3">Account Credentials</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Email Address <span class="text-red-400">*</span>
                        </label>
                        <input name="email" type="email" required value="{{ old('email') }}"
                               placeholder="patient@example.com"
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('email') border-red-300 bg-red-50 @enderror">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                Password <span class="text-red-400">*</span>
                            </label>
                            <input name="password" type="password" required placeholder="Min. 8 characters"
                                   class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('password') border-red-300 @enderror">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                Confirm Password <span class="text-red-400">*</span>
                            </label>
                            <input name="password_confirmation" type="password" required placeholder="Re-enter password"
                                   class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('patients.index') }}"
                   class="flex-1 text-center py-2.5 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 rounded-xl text-sm transition-all shadow-lg shadow-blue-500/25 hover:-translate-y-0.5">
                    Register Patient
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
