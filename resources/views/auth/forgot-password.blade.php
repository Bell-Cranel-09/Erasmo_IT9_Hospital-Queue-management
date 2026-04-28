@extends('layouts.auth')

@section('title', 'Forgot Password – St. Gabriel Medical Center')

@section('content')

{{-- Back to login --}}
<a href="{{ route('login') }}"
   class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-600 transition mb-6">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    Back to Login
</a>

{{-- Icon --}}
<div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 mb-4">
    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
    </svg>
</div>

<h2 class="text-xl font-semibold text-slate-800 mb-1">Forgot your password?</h2>
<p class="text-sm text-slate-500 mb-6">
    No worries. Enter your email and we'll send you a link to reset your password.
</p>

{{-- Success message --}}
@if(session('status'))
<div class="flex items-start gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-5">
    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <p class="text-sm font-medium">Reset link sent!</p>
        <p class="text-sm mt-0.5">{{ session('status') }}</p>
    </div>
</div>
@endif

{{-- Errors --}}
@if($errors->any())
<div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-5">
    @foreach($errors->all() as $error)
        <p class="text-sm text-red-600">{{ $error }}</p>
    @endforeach
</div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
    @csrf

    <div>
        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
            Email Address
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                </svg>
            </div>
            <input id="email" name="email" type="email"
                   value="{{ old('email') }}"
                   required autocomplete="email"
                   placeholder="you@hospital.com"
                   class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('email') border-red-300 bg-red-50 @enderror">
        </div>
    </div>

    <button type="submit"
            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 px-4 rounded-xl transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:-translate-y-0.5 active:translate-y-0 text-sm">
        Send Reset Link
    </button>
</form>

<div class="mt-6 pt-5 border-t border-slate-100 text-center">
    <p class="text-sm text-slate-500">
        Remember your password?
        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium">Sign in</a>
    </p>
</div>

@endsection
