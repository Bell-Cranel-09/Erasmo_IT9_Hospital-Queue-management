@extends('layouts.auth')

@section('title', 'Register – St. Gabriel Medical Center')

@section('content')

<h2 class="text-xl font-semibold text-slate-800 mb-1">Create your account</h2>
<p class="text-sm text-slate-500 mb-6">Register as a patient to book appointments</p>

@if($errors->any())
<div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-5">
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

<form method="POST" action="{{ route('patients.store') }}" class="space-y-4">
    @csrf

    {{-- Name row --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">First Name</label>
            <input name="first_name" type="text" value="{{ old('first_name') }}" required
                   placeholder="Juan"
                   class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('first_name') border-red-300 @enderror">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Last Name</label>
            <input name="last_name" type="text" value="{{ old('last_name') }}" required
                   placeholder="Dela Cruz"
                   class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('last_name') border-red-300 @enderror">
        </div>
    </div>

    {{-- Email --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
        <input name="email" type="email" value="{{ old('email') }}" required
               placeholder="juan@example.com"
               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('email') border-red-300 @enderror">
    </div>

    {{-- Date of birth + Gender --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Date of Birth</label>
            <input name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" required
                   class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('date_of_birth') border-red-300 @enderror">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Gender</label>
            <select name="gender" required
                    class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-white @error('gender') border-red-300 @enderror">
                <option value="">Select...</option>
                <option value="male"   {{ old('gender') == 'male'   ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                <option value="other"  {{ old('gender') == 'other'  ? 'selected' : '' }}>Other</option>
            </select>
        </div>
    </div>

    {{-- Phone --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone <span class="text-slate-400 font-normal">(optional)</span></label>
        <input name="phone" type="tel" value="{{ old('phone') }}"
               placeholder="09171234567"
               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
    </div>

    {{-- Password --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
            <input name="password" type="password" required placeholder="••••••••"
                   class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('password') border-red-300 @enderror">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm</label>
            <input name="password_confirmation" type="password" required placeholder="••••••••"
                   class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
        </div>
    </div>

    <button type="submit"
            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 px-4 rounded-xl transition-all shadow-lg shadow-blue-500/25 hover:-translate-y-0.5 active:translate-y-0 text-sm mt-2">
        Create Account
    </button>
</form>

<div class="mt-5 pt-5 border-t border-slate-100 text-center">
    <p class="text-sm text-slate-500">
        Already have an account?
        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium">Sign in</a>
    </p>
</div>

@endsection
