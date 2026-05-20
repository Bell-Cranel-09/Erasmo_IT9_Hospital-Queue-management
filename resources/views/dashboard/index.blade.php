@extends('layouts.app')

@section('title', 'Dashboard – St. Gabriel Medical Center')
@section('page-title', 'Dashboard')

@section('content')

{{-- Welcome banner --}}
<div class="relative bg-gradient-to-r from-blue-700 via-blue-600 to-blue-500 rounded-2xl p-6 mb-6 overflow-hidden shadow-xl">
    <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-white/10"></div>
    <div class="absolute -bottom-6 right-20 w-24 h-24 rounded-full bg-white/10"></div>
    <div class="relative z-10">
        <p class="text-blue-100 text-sm font-medium mb-1">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }},
        </p>
        <h2 class="text-white text-2xl font-display mb-1">{{ auth()->user()->name }}</h2>
        <p class="text-blue-200 text-sm">Here's what's happening at the clinic today.</p>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
        <div class="flex items-center justify-between">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="text-right">
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full block mb-2">All time</span>
                <p class="text-3xl font-bold text-slate-800">{{ $totalPatients }}</p>
                <p class="text-sm text-slate-500 mt-0.5">Total Patients</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
        <div class="flex items-center justify-between">
            <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="text-right">
                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full block mb-2">Today</span>
                <p class="text-3xl font-bold text-slate-800">{{ $todayAppointments }}</p>
                <p class="text-sm text-slate-500 mt-0.5">Appointments</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
        <div class="flex items-center justify-between">
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-right">
                <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full block mb-2">Live</span>
                <p class="text-3xl font-bold text-slate-800">{{ $waitingCount }}</p>
                <p class="text-sm text-slate-500 mt-0.5">Waiting in Queue</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 card-hover">
        <div class="flex items-center justify-between">
            <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-right">
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full block mb-2">Now</span>
                <p class="text-3xl font-bold text-slate-800">{{ $currentQueue?->queue_code ?? '—' }}</p>
                <p class="text-sm text-slate-500 mt-0.5">Now Serving</p>
            </div>
        </div>
    </div>

</div>

{{-- ── ANALYTICS CHART — Admin & Staff only ────────────────────────────── --}}
@if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isStaff()))
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

    {{-- Weekly Bar Chart --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="font-semibold text-slate-800">Weekly Queue Activity</h3>
                <p id="chart-week-label" class="text-xs text-slate-400 mt-0.5">Loading...</p>
            </div>
            <div class="flex items-center gap-5">
                <div class="text-center">
                    <p class="text-xs text-slate-400">Total This Week</p>
                    <p id="chart-total" class="text-xl font-bold text-blue-600">—</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-slate-400">Busiest Day</p>
                    <p id="chart-peak-day" class="text-sm font-bold text-slate-700">—</p>
                </div>
            </div>
        </div>

        <div class="px-6 pt-5 pb-4">
            {{-- Y-axis labels + bars --}}
            <div class="flex gap-3">
                {{-- Y axis --}}
                <div id="y-axis" class="flex flex-col justify-between text-right pr-1 pb-6"
                     style="min-width:28px; height:180px;">
                    {{-- Filled by JS --}}
                </div>

                {{-- Chart body --}}
                <div class="flex-1 relative">
                    {{-- Grid lines --}}
                    <div class="absolute inset-0 pb-6 flex flex-col justify-between pointer-events-none">
                        <div class="border-t border-slate-100 w-full"></div>
                        <div class="border-t border-slate-100 w-full"></div>
                        <div class="border-t border-slate-100 w-full"></div>
                        <div class="border-t border-slate-100 w-full"></div>
                        <div class="border-t border-slate-200 w-full"></div>
                    </div>

                    {{-- Bars --}}
                    <div id="bars-container"
                         class="relative z-10 flex items-end justify-around gap-2"
                         style="height:180px; padding-bottom:24px;">
                        {{-- Loading skeletons --}}
                        @for($i = 0; $i < 7; $i++)
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <div class="w-full rounded-t-lg bg-slate-100 animate-pulse" style="height:30%"></div>
                            <div class="h-3 w-8 bg-slate-100 rounded animate-pulse mt-1"></div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- Legend --}}
            <div class="flex items-center gap-4 mt-3 pl-8">
                <span class="flex items-center gap-1.5 text-xs text-slate-400">
                    <span class="w-3 h-3 rounded-sm bg-blue-500 inline-block"></span> Today
                </span>
                <span class="flex items-center gap-1.5 text-xs text-slate-400">
                    <span class="w-3 h-3 rounded-sm bg-indigo-500 inline-block"></span> Busiest
                </span>
                <span class="flex items-center gap-1.5 text-xs text-slate-400">
                    <span class="w-3 h-3 rounded-sm bg-slate-300 inline-block"></span> Other Days
                </span>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
<div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
    <h3 class="text-sm font-semibold text-slate-700 mb-4">Quick Actions</h3>
    <div class="space-y-2">

        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isStaff()))
        {{-- ADMIN / STAFF actions --}}
        <a href="{{ route('queue.index') }}"
           class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 hover:bg-blue-100 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-800">Add to Queue</p>
                <p class="text-xs text-slate-500">Register walk-in patient</p>
            </div>
        </a>

        <a href="{{ route('appointments.create') }}"
           class="flex items-center gap-3 p-3 rounded-xl bg-purple-50 hover:bg-purple-100 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-purple-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-800">Book Appointment</p>
                <p class="text-xs text-slate-500">Schedule a doctor visit</p>
            </div>
        </a>

        @elseif(auth()->check() && auth()->user()->isPatient())
        {{-- PATIENT actions --}}
        <a href="{{ route('appointments.create') }}"
           class="flex items-center gap-3 p-3 rounded-xl bg-purple-50 hover:bg-purple-100 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-purple-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-800">Book Appointment</p>
                <p class="text-xs text-slate-500">Schedule a doctor visit</p>
            </div>
        </a>

        <a href="{{ route('my-profile') }}"
           class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-slate-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-800">My Profile</p>
                <p class="text-xs text-slate-500">View your patient record</p>
            </div>
        </a>
        @endif

    </div>
</div>
@endif

{{-- Today's Queue Preview --}}
<div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-slate-700">Today's Queue</h3>
        <a href="{{ route('queue.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View all →</a>
    </div>

    @if($recentQueues->isEmpty())
    <div class="text-center py-8">
        <p class="text-sm text-slate-500">No patients in queue today</p>
    </div>
    @else
    <div class="space-y-2">
        @foreach($recentQueues as $queue)
        <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">
                {{ $queue->queue_number }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-800 truncate">{{ $queue->patient->full_name }}</p>
                <p class="text-xs text-slate-500">{{ $queue->department->name }} · {{ $queue->queue_code }}</p>
            </div>
            <span class="badge-{{ $queue->status }} text-xs font-semibold px-2.5 py-1 rounded-full flex-shrink-0 capitalize">
                {{ $queue->status }}
            </span>
        </div>
        @endforeach
    </div>
    @endif
</div>

@push('scripts')
@if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isStaff()))
<script>
async function loadWeeklyChart() {
    try {
        const res  = await fetch('/analytics/weekly-queue', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        // Update summary labels
        document.getElementById('chart-week-label').textContent = data.week;
        document.getElementById('chart-total').textContent      = data.total;
        document.getElementById('chart-peak-day').textContent   = data.peak_day;

        const maxCount = Math.max(data.peak, 1);

        // Build Y-axis labels (0, 25%, 50%, 75%, 100% of max)
        const yAxis = document.getElementById('y-axis');
        yAxis.innerHTML = [maxCount, Math.round(maxCount*0.75), Math.round(maxCount*0.5), Math.round(maxCount*0.25), 0]
            .map(v => `<span class="text-xs text-slate-400 leading-none">${v}</span>`)
            .join('');

        // Build bars
        const container = document.getElementById('bars-container');
        container.innerHTML = data.days.map(day => {
            const heightPct  = day.count > 0 ? Math.max((day.count / maxCount) * 100, 6) : 3;
            const isPeak     = day.count === data.peak && data.peak > 0;
            const isToday    = day.is_today;

            const barColor   = isToday  ? 'bg-gradient-to-t from-blue-600 to-blue-400'
                             : isPeak   ? 'bg-gradient-to-t from-indigo-600 to-indigo-400'
                             :            'bg-gradient-to-t from-slate-400 to-slate-300';

            const labelColor = isToday  ? 'text-blue-600 font-bold'
                             : isPeak   ? 'text-indigo-600 font-semibold'
                             :            'text-slate-500';

            const todayDot   = isToday
                ? `<span class="w-1.5 h-1.5 rounded-full bg-blue-500 block mx-auto mt-0.5"></span>`
                : `<span class="w-1.5 h-1.5 block"></span>`;

            return `
                <div class="flex-1 flex flex-col items-center gap-0 group relative"
                     title="${day.day_full}: ${day.count} patient${day.count !== 1 ? 's' : ''}">

                    {{-- Hover tooltip --}}
                    <div class="absolute -top-9 left-1/2 -translate-x-1/2 bg-slate-800 text-white
                                text-xs px-2 py-1 rounded-lg opacity-0 group-hover:opacity-100
                                transition-all duration-150 whitespace-nowrap pointer-events-none z-20 shadow-lg">
                        ${day.day_full}<br>
                        <span class="font-bold">${day.count}</span> patient${day.count !== 1 ? 's' : ''}
                    </div>

                    {{-- Count above bar --}}
                    <span class="text-xs font-bold ${labelColor} mb-1 transition-opacity duration-200
                                 ${day.count > 0 ? 'opacity-100' : 'opacity-40'}">
                        ${day.count}
                    </span>

                    {{-- Bar wrapper --}}
                    <div class="w-full flex items-end" style="height: 120px;">
                        <div class="w-full rounded-t-xl ${barColor} shadow-sm cursor-pointer
                                    transition-all duration-700 ease-out
                                    hover:brightness-110 hover:shadow-md hover:-translate-y-0.5"
                             style="height: 0%;"
                             data-target-height="${heightPct}">
                        </div>
                    </div>

                    {{-- Day label --}}
                    <span class="text-xs mt-1.5 ${labelColor}">${day.day}</span>
                    ${todayDot}
                </div>
            `;
        }).join('');

        // Animate bars growing up with staggered delay
        requestAnimationFrame(() => {
            container.querySelectorAll('[data-target-height]').forEach((bar, i) => {
                setTimeout(() => {
                    bar.style.height = bar.dataset.targetHeight + '%';
                }, i * 80); // 80ms stagger between each bar
            });
        });

    } catch (e) {
        console.error('Analytics error:', e);
        const label = document.getElementById('chart-week-label');
        if (label) label.textContent = 'Could not load chart data';
    }
}

// Load on page ready
document.addEventListener('DOMContentLoaded', loadWeeklyChart);

// Refresh every 60 seconds
setInterval(loadWeeklyChart, 60000);
</script>
@endif
@endpush

@endsection
