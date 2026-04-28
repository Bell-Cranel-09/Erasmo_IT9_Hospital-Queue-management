<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>St. Gabriel Medical Center – Queue Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans:    ['DM Sans', 'sans-serif'],
                        display: ['Instrument Serif', 'serif'],
                    },
                    animation: {
                        'fade-up':    'fadeUp 0.7s ease-out forwards',
                        'fade-in':    'fadeIn 0.6s ease-out forwards',
                        'slide-left': 'slideLeft 0.7s ease-out forwards',
                    },
                    keyframes: {
                        fadeUp:    { from: { opacity: 0, transform: 'translateY(24px)' }, to: { opacity: 1, transform: 'translateY(0)' } },
                        fadeIn:    { from: { opacity: 0 }, to: { opacity: 1 } },
                        slideLeft: { from: { opacity: 0, transform: 'translateX(24px)' }, to: { opacity: 1, transform: 'translateX(0)' } },
                    }
                }
            }
        }
    </script>
    <style>
        .hero-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #1d4ed8 100%);
        }
        .dot-grid {
            background-image: radial-gradient(circle, rgba(255,255,255,0.07) 1px, transparent 1px);
            background-size: 26px 26px;
        }
        .glass-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.15);
        }
        .feature-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.10);
        }
        /* Staggered animation delays */
        .delay-100 { animation-delay: 0.1s; opacity: 0; }
        .delay-200 { animation-delay: 0.2s; opacity: 0; }
        .delay-300 { animation-delay: 0.3s; opacity: 0; }
        .delay-400 { animation-delay: 0.4s; opacity: 0; }
        .delay-500 { animation-delay: 0.5s; opacity: 0; }
    </style>
</head>
<body class="font-sans bg-white antialiased">

{{-- ── NAVBAR ───────────────────────────────────────────────────────────── --}}
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur border-b border-slate-100 shadow-sm">
    <div class="max-w-6xl mx-auto px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center shadow">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-blue-700 uppercase tracking-widest leading-none">St. Gabriel</p>
                <p class="text-sm font-semibold text-slate-800 leading-tight">Medical Center</p>
            </div>
        </div>

        <div class="hidden md:flex items-center gap-8">
            <a href="#features" class="text-sm text-slate-600 hover:text-blue-600 font-medium transition">Features</a>
            <a href="#how-it-works" class="text-sm text-slate-600 hover:text-blue-600 font-medium transition">How It Works</a>
            <a href="#contact" class="text-sm text-slate-600 hover:text-blue-600 font-medium transition">Contact</a>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}"
               class="text-sm font-semibold text-slate-700 hover:text-blue-600 transition px-3 py-2 rounded-lg hover:bg-blue-50">
                Sign In
            </a>
            <a href="{{ route('register') }}"
               class="text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl transition shadow-md shadow-blue-500/25 hover:-translate-y-0.5">
                Register
            </a>
        </div>
    </div>
</nav>

{{-- ── HERO SECTION ─────────────────────────────────────────────────────── --}}
<section class="hero-bg dot-grid min-h-screen pt-16 flex items-center relative overflow-hidden">

    {{-- Decorative blobs --}}
    <div class="absolute top-20 right-0 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-10 left-10 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-6xl mx-auto px-6 py-20 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            {{-- Left: Text --}}
            <div>
                <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 rounded-full px-4 py-1.5 mb-6 animate-fade-in delay-100">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                    <span class="text-blue-200 text-xs font-semibold uppercase tracking-widest">Now Live</span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-display text-white leading-tight mb-5 animate-fade-up delay-200">
                    Smarter Care,<br>
                    <span class="text-blue-300 italic">Zero Waiting</span><br>
                    Frustration
                </h1>

                <p class="text-blue-200 text-base md:text-lg leading-relaxed mb-8 max-w-lg animate-fade-up delay-300">
                    St. Gabriel Medical Center's digital queue and appointment system.
                    Book visits, track your queue number, and get seen faster — all in one place.
                </p>

                <div class="flex flex-wrap gap-3 animate-fade-up delay-400">
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 bg-white text-blue-700 font-bold px-6 py-3 rounded-xl hover:bg-blue-50 transition shadow-xl hover:-translate-y-0.5 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Register as Patient
                    </a>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 bg-white/10 border border-white/30 text-white font-semibold px-6 py-3 rounded-xl hover:bg-white/20 transition text-sm">
                        Sign In
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>

                {{-- Quick stats --}}
                <div class="flex gap-8 mt-10 animate-fade-up delay-500">
                    <div>
                        <p class="text-2xl font-bold text-white">5+</p>
                        <p class="text-blue-300 text-xs">Departments</p>
                    </div>
                    <div class="border-l border-white/20 pl-8">
                        <p class="text-2xl font-bold text-white">24/7</p>
                        <p class="text-blue-300 text-xs">Online Booking</p>
                    </div>
                    <div class="border-l border-white/20 pl-8">
                        <p class="text-2xl font-bold text-white">100%</p>
                        <p class="text-blue-300 text-xs">Digital Queue</p>
                    </div>
                </div>
            </div>

            {{-- Right: Hero Image Card --}}
            <div class="animate-slide-left delay-300">
                <div class="relative">

                    {{-- ════════════════════════════════════════════════════
                         HERO IMAGE — Replace the src below with your photo.
                         Recommended size: 800x600px or 16:9 / 4:3 ratio.
                         Put your image in: public/images/hero.jpg
                         Then change the src to: {{ asset('images/hero.jpg') }}
                    ═══════════════════════════════════════════════════════ --}}
                    <div class="rounded-2xl overflow-hidden shadow-2xl border border-white/20">
                        <img src="{{ asset('images/hero.jpg') }}"
                             onerror="this.parentElement.innerHTML = placeholderHTML()"
                             alt="St. Gabriel Medical Center"
                             class="w-full h-80 object-cover">
                    </div>

                    {{-- Floating badge: queue display --}}
                    <div class="absolute -bottom-5 -left-5 glass-card rounded-2xl p-4 shadow-xl">
                        <p class="text-blue-200 text-xs font-semibold uppercase tracking-wide mb-1">Now Serving</p>
                        <p class="text-white text-4xl font-display font-bold leading-none">042</p>
                        <p class="text-blue-300 text-xs mt-1">General Medicine</p>
                    </div>

                    {{-- Floating badge: appointment --}}
                    <div class="absolute -top-4 -right-4 glass-card rounded-2xl px-4 py-3 shadow-xl">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                            <p class="text-white text-xs font-semibold">Appointment Confirmed</p>
                        </div>
                        <p class="text-blue-200 text-xs mt-0.5">Today · 10:30 AM</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── FEATURES ─────────────────────────────────────────────────────────── --}}
<section id="features" class="py-20 bg-slate-50">
    <div class="max-w-6xl mx-auto px-6">

        <div class="text-center mb-12">
            <p class="text-blue-600 text-sm font-semibold uppercase tracking-widest mb-2">What We Offer</p>
            <h2 class="text-3xl md:text-4xl font-display text-slate-800">Everything you need,<br><em>in one system</em></h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

            {{-- Feature 1 --}}
            <div class="feature-card bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-800 mb-2">Digital Queue System</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Get a queue number instantly, track your position in real time, and know exactly when you'll be called.</p>
            </div>

            {{-- Feature 2 --}}
            <div class="feature-card bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-800 mb-2">Appointment Scheduling</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Book appointments with your preferred doctor online. Choose your date, time slot, and get an instant confirmation code.</p>
            </div>

            {{-- Feature 3 --}}
            <div class="feature-card bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-800 mb-2">Patient Records</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Your complete appointment history and queue records stored securely and accessible anytime.</p>
            </div>

            {{-- Feature 4 --}}
            <div class="feature-card bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-800 mb-2">Live Dashboard</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Staff get real-time stats — patients waiting, currently being served, and daily appointment summaries.</p>
            </div>

            {{-- Feature 5 --}}
            <div class="feature-card bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-800 mb-2">Multi-Department</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Separate queues for General Medicine, Pediatrics, Cardiology, Orthopedics, and Emergency services.</p>
            </div>

            {{-- Feature 6 --}}
            <div class="feature-card bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-800 mb-2">Secure & Role-Based</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Admin, Staff, and Patient roles with controlled access. Your data is protected and private.</p>
            </div>

        </div>
    </div>
</section>

{{-- ── HOW IT WORKS ─────────────────────────────────────────────────────── --}}
<section id="how-it-works" class="py-20 bg-white">
    <div class="max-w-5xl mx-auto px-6">

        <div class="text-center mb-14">
            <p class="text-blue-600 text-sm font-semibold uppercase tracking-widest mb-2">Simple Process</p>
            <h2 class="text-3xl md:text-4xl font-display text-slate-800">How it works</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">

            {{-- Connecting line (desktop only) --}}
            <div class="hidden md:block absolute top-8 left-1/3 right-1/3 h-0.5 bg-gradient-to-r from-blue-200 to-blue-400"></div>

            <div class="text-center">
                <div class="w-16 h-16 rounded-2xl bg-blue-600 text-white text-2xl font-bold flex items-center justify-center mx-auto mb-5 shadow-lg shadow-blue-500/30">
                    1
                </div>
                <h3 class="font-semibold text-slate-800 mb-2">Register</h3>
                <p class="text-sm text-slate-500">Create your patient account in under 2 minutes with your basic information.</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 rounded-2xl bg-blue-700 text-white text-2xl font-bold flex items-center justify-center mx-auto mb-5 shadow-lg shadow-blue-600/30">
                    2
                </div>
                <h3 class="font-semibold text-slate-800 mb-2">Book or Walk In</h3>
                <p class="text-sm text-slate-500">Schedule an appointment online or walk in to get an instant queue number.</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 rounded-2xl bg-blue-800 text-white text-2xl font-bold flex items-center justify-center mx-auto mb-5 shadow-lg shadow-blue-700/30">
                    3
                </div>
                <h3 class="font-semibold text-slate-800 mb-2">Get Seen</h3>
                <p class="text-sm text-slate-500">Track your queue position live and proceed to the counter when your number is called.</p>
            </div>
        </div>
    </div>
</section>

{{-- ── CTA BANNER ───────────────────────────────────────────────────────── --}}
<section class="hero-bg dot-grid py-20">
    <div class="max-w-3xl mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-display text-white mb-4">
            Ready to skip the waiting room?
        </h2>
        <p class="text-blue-200 text-base mb-8">
            Register today and experience a smarter, faster way to access healthcare at St. Gabriel Medical Center.
        </p>
        <div class="flex flex-wrap gap-3 justify-center">
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 bg-white text-blue-700 font-bold px-8 py-3 rounded-xl hover:bg-blue-50 transition shadow-xl hover:-translate-y-0.5 text-sm">
                Get Started — It's Free
            </a>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 bg-white/10 border border-white/30 text-white font-semibold px-8 py-3 rounded-xl hover:bg-white/20 transition text-sm">
                Sign In
            </a>
        </div>
    </div>
</section>

{{-- ── FOOTER ───────────────────────────────────────────────────────────── --}}
<footer id="contact" class="bg-slate-900 py-12">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-blue-400 uppercase tracking-widest leading-none">St. Gabriel</p>
                        <p class="text-sm font-semibold text-white">Medical Center</p>
                    </div>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Providing quality healthcare with a smarter, digital-first patient experience.
                </p>
            </div>

            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('login') }}" class="text-slate-400 hover:text-white text-sm transition">Patient Login</a></li>
                    <li><a href="{{ route('register') }}" class="text-slate-400 hover:text-white text-sm transition">Register</a></li>
                    <li><a href="#features" class="text-slate-400 hover:text-white text-sm transition">Features</a></li>
                    <li><a href="#how-it-works" class="text-slate-400 hover:text-white text-sm transition">How It Works</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Contact</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{-- Replace with your actual address --}}
                        123 Medical Drive, Davao City
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{-- Replace with your actual number --}}
                        (082) 123-4567
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{-- Replace with your actual email --}}
                        info@stgabriel.com
                    </li>
                </ul>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-800 text-center">
            <p class="text-slate-500 text-sm">© {{ date('Y') }} St. Gabriel Medical Center · Queue Management System</p>
        </div>
    </div>
</footer>

{{-- Smooth scroll --}}
<script>
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const target = document.querySelector(a.getAttribute('href'));
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
        });
    });

    // Placeholder if hero image is missing
    function placeholderHTML() {
        return `<div class="w-full h-80 bg-gradient-to-br from-blue-800 to-blue-600 flex flex-col items-center justify-center gap-3">
            <svg class="w-16 h-16 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-white/50 text-sm font-medium">Add your hospital photo here</p>
            <p class="text-white/30 text-xs">Place image at: public/images/hero.jpg</p>
        </div>`;
    }
</script>

</body>
</html>
