<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'St. Gabriel Medical Center')</title>

    {{-- Tailwind CSS via CDN (replace with Vite in production) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Google Fonts: Instrument Serif + DM Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Heroicons --}}
    <script src="https://unpkg.com/heroicons@2.0.18/dist/heroicons.js" defer></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans:    ['DM Sans', 'sans-serif'],
                        display: ['Instrument Serif', 'serif'],
                    },
                    colors: {
                        primary: {
                            50:  '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        sage: {
                            50:  '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                        },
                    },
                    animation: {
                        'fade-in':    'fadeIn 0.4s ease-out',
                        'slide-up':   'slideUp 0.4s ease-out',
                        'slide-in':   'slideIn 0.3s ease-out',
                        'pulse-soft': 'pulseSoft 2s infinite',
                    },
                    keyframes: {
                        fadeIn:    { from: { opacity: 0 }, to: { opacity: 1 } },
                        slideUp:   { from: { opacity: 0, transform: 'translateY(16px)' }, to: { opacity: 1, transform: 'translateY(0)' } },
                        slideIn:   { from: { opacity: 0, transform: 'translateX(-12px)' }, to: { opacity: 1, transform: 'translateX(0)' } },
                        pulseSoft: { '0%,100%': { opacity: 1 }, '50%': { opacity: 0.6 } },
                    }
                }
            }
        }
    </script>

    <style>
        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 3px; }

        /* Active nav link */
        .nav-active {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white !important;
            box-shadow: 0 4px 12px rgba(37,99,235,0.35);
        }

        /* Card hover */
        .card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.10);
        }

        /* Status badges */
        .badge-waiting   { background:#fef3c7; color:#92400e; }
        .badge-serving   { background:#dbeafe; color:#1e40af; }
        .badge-done      { background:#dcfce7; color:#15803d; }
        .badge-skipped   { background:#f3f4f6; color:#6b7280; }
        .badge-pending   { background:#fef3c7; color:#92400e; }
        .badge-confirmed { background:#dbeafe; color:#1e40af; }
        .badge-completed { background:#dcfce7; color:#15803d; }
        .badge-canceled  { background:#fee2e2; color:#991b1b; }

        /* Sidebar width */
        #sidebar { width: 260px; min-height: 100vh; }

        /* Main content offset */
        #main-content { margin-left: 260px; }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); position: fixed; z-index: 50; transition: transform 0.3s; }
            #sidebar.open { transform: translateX(0); }
            #main-content { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-slate-50 font-sans h-full">

{{-- ── SIDEBAR ──────────────────────────────────────────────────────── --}}
<aside id="sidebar" class="fixed top-0 left-0 bg-white border-r border-slate-100 flex flex-col shadow-xl">

    {{-- Logo --}}
    <div class="px-6 py-5 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center shadow-lg flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-blue-600 uppercase tracking-widest leading-none">St. Gabriel</p>
                <p class="text-sm font-bold text-slate-800 leading-tight">Medical Center</p>
            </div>
        </div>
    </div>

    {{-- User Info --}}
    @auth
    <div class="px-5 py-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</p>
            </div>
        </div>
    </div>
    @endauth

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest px-3 mb-2">Main</p>

        <a href="{{ route('dashboard') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all {{ request()->routeIs('dashboard') ? 'nav-active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('queue.index') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all {{ request()->routeIs('queue.*') ? 'nav-active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Patient Queue
            @php
                $waitingCount = \App\Models\Queue::whereDate('queue_date', today())->where('status','waiting')->count();
            @endphp
            @if($waitingCount > 0)
            <span class="ml-auto text-xs font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ $waitingCount }}</span>
            @endif
        </a>

        <a href="{{ route('appointments.index') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all {{ request()->routeIs('appointments.*') ? 'nav-active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Appointments
        </a>

        <a href="{{ route('doctors.index') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all {{ request()->routeIs('doctors.*') ? 'nav-active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Doctors
        </a>

        <a href="{{ route('patients.index') }}"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all {{ request()->routeIs('patients.*') ? 'nav-active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Patients
        </a>
    </nav>

    {{-- Logout --}}
    @auth
    <div class="px-3 py-4 border-t border-slate-100">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:bg-red-50 hover:text-red-600 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
    @endauth
</aside>

{{-- ── MOBILE OVERLAY ──────────────────────────────────────────────── --}}
<div id="overlay" class="hidden fixed inset-0 bg-black/40 z-40 md:hidden" onclick="toggleSidebar()"></div>

{{-- ── MAIN CONTENT ─────────────────────────────────────────────────── --}}
<div id="main-content" class="min-h-screen flex flex-col">

    {{-- Top bar --}}
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-100 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-4">
            {{-- Mobile menu button --}}
            <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div>
                <h1 class="text-base font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-slate-400">{{ now()->format('l, F j, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            {{-- Live clock --}}
            <span id="live-clock" class="text-sm font-mono text-slate-500 bg-slate-100 px-3 py-1 rounded-lg"></span>
        </div>
    </header>

    {{-- Flash messages --}}
    <div class="px-6 pt-4 space-y-2">
        @if(session('success'))
        <div class="flex items-center gap-3 bg-sage-50 border border-sage-200 text-sage-700 px-4 py-3 rounded-xl text-sm animate-slide-up" id="flash-success">
            <svg class="w-5 h-5 text-sage-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
            <button onclick="this.parentElement.remove()" class="ml-auto text-sage-400 hover:text-sage-600">✕</button>
        </div>
        @endif

        @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm animate-slide-up" id="flash-error">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
            <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600">✕</button>
        </div>
        @endif

        @if(session('info'))
        <div class="flex items-center gap-3 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl text-sm animate-slide-up">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('info') }}
            <button onclick="this.parentElement.remove()" class="ml-auto text-blue-400 hover:text-blue-600">✕</button>
        </div>
        @endif
    </div>

    {{-- Page content --}}
    <main class="flex-1 p-6 animate-fade-in">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="px-6 py-3 border-t border-slate-100 text-center text-xs text-slate-400">
        © {{ date('Y') }} St. Gabriel Medical Center · Queue Management System v1.0
    </footer>
</div>

<script>
    // Live clock
    function updateClock() {
        const now = new Date();
        document.getElementById('live-clock').textContent =
            now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Mobile sidebar toggle
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('hidden');
    }

    // Auto-dismiss flash messages after 5 seconds
    setTimeout(() => {
        ['flash-success','flash-error'].forEach(id => {
            const el = document.getElementById(id);
            if (el) { el.style.opacity = '0'; el.style.transition = 'opacity 0.5s'; setTimeout(() => el.remove(), 500); }
        });
    }, 5000);
</script>

@stack('scripts')
</body>
</html>
