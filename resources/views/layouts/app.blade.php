<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title', 'St. Gabriel Medical Center')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'DM Serif Display', serif; }

        .badge-waiting   { background:#fef3c7; color:#b45309; }
        .badge-serving   { background:#dbeafe; color:#1d4ed8; }
        .badge-done      { background:#dcfce7; color:#15803d; }
        .badge-skipped   { background:#f1f5f9; color:#64748b; }
        .badge-pending   { background:#fef9c3; color:#a16207; }
        .badge-confirmed { background:#dbeafe; color:#1d4ed8; }
        .badge-canceled  { background:#fee2e2; color:#dc2626; }
        .badge-completed { background:#dcfce7; color:#15803d; }

        .card-hover { transition: all 0.2s; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }

        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; border-radius: 12px;
            font-size: 0.875rem; font-weight: 500;
            color: #475569; transition: all 0.15s;
        }
        .nav-link:hover { background: #f8fafc; color: #2563eb; }
        .nav-link.active { background: #2563eb; color: #fff; box-shadow: 0 4px 12px rgba(37,99,235,0.25); }
        .nav-link.active:hover { background: #1d4ed8; color: #fff; }

        /* Mobile sidebar overlay */
        #sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 40;
            backdrop-filter: blur(2px);
        }
        #sidebar-overlay.open { display: block; }

        /* Sidebar mobile: slide in from left */
        #sidebar {
            position: fixed; top: 0; left: 0;
            height: 100%; width: 220px;
            transform: translateX(-100%);
            transition: transform 0.25s cubic-bezier(0.4,0,0.2,1);
            z-index: 50;
            background: #fff;
            border-right: 1px solid #f1f5f9;
            box-shadow: 4px 0 24px rgba(0,0,0,0.08);
        }
        #sidebar.open { transform: translateX(0); }

        /* Desktop: sidebar always visible */
        @media (min-width: 768px) {
            #sidebar {
                position: relative;
                transform: translateX(0) !important;
                flex-shrink: 0;
                z-index: auto;
                box-shadow: none;
            }
            #sidebar-overlay { display: none !important; }
            #mobile-topbar { display: none !important; }
        }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
    </style>
</head>
<body class="h-full bg-slate-50">

{{-- Mobile overlay --}}
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

<div class="flex h-screen overflow-hidden">

    {{-- ── SIDEBAR ────────────────────────────────────────────────────── --}}
    <aside id="sidebar" class="flex flex-col overflow-y-auto">

        {{-- Logo --}}
        <div class="px-5 py-5 border-b border-slate-100 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-800 leading-tight">ST. GABRIEL</p>
                    <p class="text-xs text-slate-500 leading-tight">Medical Center</p>
                </div>
            </a>
            {{-- Close button (mobile only) --}}
            <button onclick="closeSidebar()" class="md:hidden w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- User info --}}
        @auth
        <div class="px-5 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 capitalize">{{ auth()->user()->role }}</p>
                </div>
            </div>
        </div>
        @endauth

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider px-3 mb-2">MAIN</p>

            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('queue.index') }}"
               class="nav-link {{ request()->routeIs('queue.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Patient Queue
                @if(auth()->check())
                @php $wCount = \App\Models\Queue::whereDate('queue_date', today())->where('status','waiting')->count(); @endphp
                @if($wCount > 0)
                <span class="ml-auto text-xs font-bold bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-full">{{ $wCount }}</span>
                @endif
                @endif
            </a>

           <a href="{{ route('appointments.index') }}"
            class="nav-link {{ request()->routeIs('appointments.index') && !request()->routeIs('appointments.create') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Appointments
            </a>

            @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isStaff()))

            <a href="{{ route('doctors.index') }}"
               class="nav-link {{ request()->routeIs('doctors.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Doctors
            </a>

            <a href="{{ route('patients.index') }}"
               class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Patients
            </a>

            @elseif(auth()->check() && auth()->user()->isPatient())

            <a href="{{ route('appointments.create') }}"
               class="nav-link {{ request()->routeIs('appointments.create') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Book Appointment
            </a>

            <a href="{{ route('my-profile') }}"
               class="nav-link {{ request()->routeIs('patients.show') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                My Profile
            </a>

            @endif
        </nav>

        {{-- Footer --}}
        <div class="px-5 py-4 border-t border-slate-100">
            <p class="text-xs text-slate-400 mb-3">{{ now()->format('l, M j') }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 text-xs font-medium text-slate-500 hover:text-red-600 transition-colors w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ── MAIN CONTENT ───────────────────────────────────────────────── --}}
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Mobile top bar --}}
        <div id="mobile-topbar"
             class="md:hidden flex items-center justify-between bg-white border-b border-slate-100 px-4 py-3 flex-shrink-0">
            <button onclick="openSidebar()"
                    class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <span class="text-sm font-bold text-slate-800">St. Gabriel</span>
            </div>
            <div class="text-xs font-semibold text-slate-500 tabular-nums" id="mobile-clock"></div>
        </div>

        {{-- Desktop top bar --}}
        <header class="hidden md:flex items-center justify-between bg-white border-b border-slate-100 px-6 py-4 flex-shrink-0">
            <div>
                <h1 class="text-lg font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-slate-400 mt-0.5">{{ now()->format('l, F j, Y') }}</p>
            </div>
            <div class="text-sm font-semibold text-slate-600 tabular-nums" id="live-clock"></div>
        </header>

        {{-- Mobile page title --}}
        <div class="md:hidden px-4 pt-3 pb-1 flex-shrink-0">
            <h1 class="text-base font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
        </div>

        {{-- Flash messages --}}
        <div class="px-4 md:px-6 pt-2 flex-shrink-0">
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
            @endif
            @if(session('info'))
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('info') }}
            </div>
            @endif
        </div>

        {{-- Page content --}}
        <div class="flex-1 overflow-y-auto px-4 md:px-6 py-4">
            @yield('content')
        </div>

        {{-- Footer --}}
        <footer class="px-4 md:px-6 py-3 border-t border-slate-100 bg-white flex-shrink-0">
            <p class="text-xs text-slate-400 text-center">
                &copy; {{ now()->year }} St. Gabriel Medical Center &middot; Queue Management System v1.0
            </p>
        </footer>
    </main>
</div>

<script>
// Live clock
function updateClock() {
    const now  = new Date();
    const h12  = String(now.getHours() % 12 || 12).padStart(2,'0');
    const m    = String(now.getMinutes()).padStart(2,'0');
    const s    = String(now.getSeconds()).padStart(2,'0');
    const ampm = now.getHours() >= 12 ? 'PM' : 'AM';
    const str  = `${h12}:${m}:${s} ${ampm}`;
    const el   = document.getElementById('live-clock');
    const mel  = document.getElementById('mobile-clock');
    if (el)  el.textContent  = str;
    if (mel) mel.textContent = `${h12}:${m} ${ampm}`;
}
updateClock();
setInterval(updateClock, 1000);

// Sidebar open/close
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebar-overlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebar-overlay').classList.remove('open');
    document.body.style.overflow = '';
}
// Close sidebar when a nav link is clicked on mobile
document.querySelectorAll('#sidebar .nav-link').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth < 768) closeSidebar();
    });
});
</script>

@stack('scripts')
</body>
</html>
