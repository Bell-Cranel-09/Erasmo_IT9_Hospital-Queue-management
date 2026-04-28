@extends('layouts.auth')

@section('title', 'Reset Password – St. Gabriel Medical Center')

@section('content')

{{-- Icon --}}
<div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 mb-4">
    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
    </svg>
</div>

<h2 class="text-xl font-semibold text-slate-800 mb-1">Set new password</h2>
<p class="text-sm text-slate-500 mb-6">
    Choose a strong password for your account.
</p>

{{-- Errors --}}
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

<form method="POST" action="{{ route('password.store') }}" class="space-y-4">
    @csrf

    {{-- Token (hidden) --}}
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    {{-- Email --}}
    <div>
        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                </svg>
            </div>
            <input id="email" name="email" type="email"
                   value="{{ old('email', $request->email) }}"
                   required autocomplete="email"
                   placeholder="you@hospital.com"
                   class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('email') border-red-300 bg-red-50 @enderror">
        </div>
    </div>

    {{-- New Password --}}
    <div>
        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">New Password</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <input id="password" name="password" type="password"
                   required autocomplete="new-password"
                   placeholder="Min. 8 characters"
                   id="password-field"
                   class="w-full pl-10 pr-10 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('password') border-red-300 bg-red-50 @enderror">
            {{-- Show/hide toggle --}}
            <button type="button" onclick="togglePassword('password-field', 'eye-1')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                <svg id="eye-1" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>

        {{-- Password strength indicator --}}
        <div class="mt-2">
            <div class="flex gap-1 mb-1">
                <div id="str-1" class="h-1 flex-1 rounded-full bg-slate-200 transition-all"></div>
                <div id="str-2" class="h-1 flex-1 rounded-full bg-slate-200 transition-all"></div>
                <div id="str-3" class="h-1 flex-1 rounded-full bg-slate-200 transition-all"></div>
                <div id="str-4" class="h-1 flex-1 rounded-full bg-slate-200 transition-all"></div>
            </div>
            <p id="str-label" class="text-xs text-slate-400">Enter a password</p>
        </div>
    </div>

    {{-- Confirm Password --}}
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Confirm New Password</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <input id="password_confirmation" name="password_confirmation" type="password"
                   required autocomplete="new-password"
                   placeholder="Re-enter new password"
                   id="confirm-field"
                   class="w-full pl-10 pr-10 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            <button type="button" onclick="togglePassword('confirm-field', 'eye-2')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                <svg id="eye-2" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
        {{-- Match indicator --}}
        <p id="match-label" class="text-xs mt-1 hidden"></p>
    </div>

    <button type="submit"
            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 px-4 rounded-xl transition-all shadow-lg shadow-blue-500/25 hover:-translate-y-0.5 active:translate-y-0 text-sm mt-2">
        Reset Password
    </button>
</form>

<div class="mt-6 pt-5 border-t border-slate-100 text-center">
    <p class="text-sm text-slate-500">
        Remember your password?
        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium">Sign in</a>
    </p>
</div>

<script>
    // Show/hide password toggle
    function togglePassword(fieldId, eyeId) {
        const field = document.getElementById(fieldId);
        const eye   = document.getElementById(eyeId);
        const isHidden = field.type === 'password';
        field.type = isHidden ? 'text' : 'password';
        eye.style.opacity = isHidden ? '0.4' : '1';
    }

    // Password strength meter
    const passwordField = document.getElementById('password-field');
    const confirmField  = document.getElementById('confirm-field');

    passwordField.addEventListener('input', function () {
        const val      = this.value;
        const strength = getStrength(val);
        updateStrengthUI(strength);
        checkMatch();
    });

    confirmField.addEventListener('input', checkMatch);

    function getStrength(password) {
        let score = 0;
        if (password.length >= 8)                    score++;
        if (/[A-Z]/.test(password))                  score++;
        if (/[0-9]/.test(password))                  score++;
        if (/[^A-Za-z0-9]/.test(password))           score++;
        return score;
    }

    function updateStrengthUI(score) {
        const colors  = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500'];
        const labels  = ['Too weak', 'Weak', 'Fair', 'Strong'];
        const txtClrs = ['text-red-500', 'text-orange-500', 'text-yellow-600', 'text-green-600'];

        for (let i = 1; i <= 4; i++) {
            const bar = document.getElementById('str-' + i);
            bar.className = 'h-1 flex-1 rounded-full transition-all ' +
                (i <= score ? colors[score - 1] : 'bg-slate-200');
        }

        const label = document.getElementById('str-label');
        if (score === 0) {
            label.textContent = 'Enter a password';
            label.className   = 'text-xs text-slate-400';
        } else {
            label.textContent = labels[score - 1];
            label.className   = 'text-xs ' + txtClrs[score - 1];
        }
    }

    function checkMatch() {
        const matchLabel = document.getElementById('match-label');
        const pw  = passwordField.value;
        const cpw = confirmField.value;

        if (!cpw) {
            matchLabel.classList.add('hidden');
            return;
        }

        matchLabel.classList.remove('hidden');
        if (pw === cpw) {
            matchLabel.textContent = '✓ Passwords match';
            matchLabel.className   = 'text-xs mt-1 text-green-600';
        } else {
            matchLabel.textContent = '✗ Passwords do not match';
            matchLabel.className   = 'text-xs mt-1 text-red-500';
        }
    }
</script>

@endsection
