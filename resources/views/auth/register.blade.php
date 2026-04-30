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

@if(session('error'))
<div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-5 text-sm text-red-600">
    {{ session('error') }}
</div>
@endif

{{-- Action goes to the PUBLIC patients.store route --}}
<form method="POST" action="{{ route('patients.store') }}" class="space-y-4">
    @csrf

    {{-- Name row --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">First Name <span class="text-red-400">*</span></label>
            <input name="first_name" type="text" value="{{ old('first_name') }}" required
                   placeholder="Juan"
                   class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('first_name') border-red-300 bg-red-50 @enderror">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Last Name <span class="text-red-400">*</span></label>
            <input name="last_name" type="text" value="{{ old('last_name') }}" required
                   placeholder="Dela Cruz"
                   class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('last_name') border-red-300 bg-red-50 @enderror">
        </div>
    </div>

    {{-- Email --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Address <span class="text-red-400">*</span></label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                </svg>
            </div>
            <input name="email" type="email" value="{{ old('email') }}" required
                   placeholder="juan@example.com"
                   class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('email') border-red-300 bg-red-50 @enderror">
        </div>
    </div>

    {{-- Date of birth + Gender --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Date of Birth <span class="text-red-400">*</span></label>
            <input name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" required
                   class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('date_of_birth') border-red-300 @enderror">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Gender <span class="text-red-400">*</span></label>
            <select name="gender" required
                    class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white transition @error('gender') border-red-300 @enderror">
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
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Password <span class="text-red-400">*</span></label>
            <div class="relative">
                <input name="password" id="reg-password" type="password" required
                       placeholder="Min. 8 characters"
                       class="w-full px-3 pr-10 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('password') border-red-300 @enderror">
                <button type="button" onclick="toggleRegPass('reg-password','eye-reg')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-blue-600 transition" tabindex="-1">
                    <svg id="eye-reg" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm <span class="text-red-400">*</span></label>
            <div class="relative">
                <input name="password_confirmation" id="reg-confirm" type="password" required
                       placeholder="Re-enter password"
                       class="w-full px-3 pr-10 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <button type="button" onclick="toggleRegPass('reg-confirm','eye-confirm')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-blue-600 transition" tabindex="-1">
                    <svg id="eye-confirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Password match indicator --}}
    <p id="match-msg" class="text-xs hidden"></p>

    <button type="submit"
            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 px-4 rounded-xl transition-all shadow-lg shadow-blue-500/25 hover:-translate-y-0.5 text-sm mt-1">
        Create Account
    </button>
</form>

<div class="mt-5 pt-5 border-t border-slate-100 text-center">
    <p class="text-sm text-slate-500">
        Already have an account?
        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium">Sign in</a>
    </p>
</div>

<script>
function toggleRegPass(fieldId, eyeId) {
    const field = document.getElementById(fieldId);
    field.type  = field.type === 'password' ? 'text' : 'password';
    document.getElementById(eyeId).style.opacity = field.type === 'text' ? '0.4' : '1';
}

// Live password match check
const pw  = document.getElementById('reg-password');
const cpw = document.getElementById('reg-confirm');
const msg = document.getElementById('match-msg');

function checkMatch() {
    if (!cpw.value) { msg.classList.add('hidden'); return; }
    msg.classList.remove('hidden');
    if (pw.value === cpw.value) {
        msg.textContent = '✓ Passwords match';
        msg.className   = 'text-xs text-green-600';
    } else {
        msg.textContent = '✗ Passwords do not match';
        msg.className   = 'text-xs text-red-500';
    }
}
pw.addEventListener('input', checkMatch);
cpw.addEventListener('input', checkMatch);
</script>

@endsection
