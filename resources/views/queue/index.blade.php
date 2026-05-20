@extends('layouts.app')

@section('title', 'Patient Queue – St. Gabriel Medical Center')
@section('page-title', 'Patient Queue')

@section('content')

{{-- ══════════════════════════════════════════════════════════════════════
     PATIENT VIEW
══════════════════════════════════════════════════════════════════════ --}}
@if(auth()->user()->isPatient())

@php
    $patient      = auth()->user()->patient;
    $myQueue      = $patient?->activeQueue();
    $myQueue?->load('department');
    $ahead        = $myQueue
        ? \App\Models\Queue::where('department_id', $myQueue->department_id)
            ->whereDate('queue_date', today())
            ->where('status', 'waiting')
            ->where('queue_number', '<', $myQueue->queue_number)
            ->count()
        : 0;
    $nowServing   = \App\Models\Queue::whereDate('queue_date', today())
                        ->where('status', 'serving')
                        ->with('department:id,name')
                        ->get();
    $totalWaiting = \App\Models\Queue::whereDate('queue_date', today())
                        ->where('status', 'waiting')->count();
    $totalDone    = \App\Models\Queue::whereDate('queue_date', today())
                        ->where('status', 'done')->count();
@endphp

<div class="max-w-2xl mx-auto space-y-5">

    {{-- My Queue Status --}}
    <div id="my-queue-card">
    @if($myQueue)
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-7 text-center shadow-xl relative overflow-hidden">
        <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-6 -left-6 w-28 h-28 rounded-full bg-white/10"></div>
        <p class="text-blue-200 text-sm font-semibold uppercase tracking-widest mb-2 relative z-10">Your Queue Number</p>
        <p id="my-queue-number" class="text-white text-8xl font-display font-bold leading-none mb-2 relative z-10">
            {{ $myQueue->queue_number }}
        </p>
        <p id="my-queue-code" class="text-blue-200 text-base mb-4 relative z-10">{{ $myQueue->queue_code }}</p>
        <div id="my-queue-status-badge"
             class="inline-flex items-center gap-2 px-4 py-2 rounded-full relative z-10
                    {{ $myQueue->status === 'serving' ? 'bg-green-500/30 border border-green-400' : 'bg-white/15 border border-white/30' }}">
            @if($myQueue->status === 'serving')
                <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                <span class="text-white font-semibold text-sm">You are being served now!</span>
            @else
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span id="my-queue-ahead-text" class="text-white font-semibold text-sm">
                    {{ $ahead > 0 ? $ahead . ' patient(s) ahead of you' : "You're next!" }}
                </span>
            @endif
        </div>
        <p class="text-blue-300 text-xs mt-3 relative z-10">{{ $myQueue->department->name }} Department</p>
    </div>
    @else
    <div class="bg-white rounded-2xl p-8 text-center shadow-sm border border-slate-100">
        <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-800 mb-1">You're not in any queue</h3>
        <p class="text-sm text-slate-500">Visit the clinic and ask staff to add you to the queue.</p>
    </div>
    @endif
    </div>

    {{-- Stats --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-slate-800">Overall Queue Status</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ now()->format('F j, Y') }} · All Departments</p>
            </div>
            <div class="flex items-center gap-1.5 text-xs text-green-600 font-medium">
                <span id="live-dot" class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span>Live</span>
            </div>
        </div>
        <div class="grid grid-cols-3 divide-x divide-slate-100">
            <div class="p-5 text-center">
                <p id="stat-waiting" class="text-3xl font-bold text-amber-600 transition-all duration-300">{{ $totalWaiting }}</p>
                <p class="text-xs text-slate-500 font-medium mt-1">Waiting</p>
            </div>
            <div class="p-5 text-center">
                <p id="stat-serving" class="text-3xl font-bold text-blue-600 transition-all duration-300">{{ $nowServing->count() }}</p>
                <p class="text-xs text-slate-500 font-medium mt-1">Being Served</p>
            </div>
            <div class="p-5 text-center">
                <p id="stat-done" class="text-3xl font-bold text-green-600 transition-all duration-300">{{ $totalDone }}</p>
                <p class="text-xs text-slate-500 font-medium mt-1">Done Today</p>
            </div>
        </div>
    </div>

    {{-- Now Serving --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span>
            <h3 class="font-semibold text-slate-800">Now Being Served</h3>
        </div>
        <div id="now-serving-list">
            @forelse($nowServing as $serving)
            <div class="flex items-center gap-4 px-5 py-3.5 border-b border-slate-50 last:border-0">
                <div class="w-12 h-12 rounded-xl bg-green-600 text-white flex items-center justify-center font-bold text-xl flex-shrink-0">
                    {{ $serving->queue_number }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">{{ $serving->queue_code }}</p>
                    <p class="text-xs text-slate-500">{{ $serving->department->name }}</p>
                </div>
                <span class="ml-auto text-xs font-semibold text-green-600 bg-green-50 px-2.5 py-1 rounded-full">Serving</span>
            </div>
            @empty
            <div class="text-center py-8 text-slate-400 text-sm">No patients being served right now</div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-slate-800 text-sm">My Appointments</h3>
                <p class="text-xs text-slate-400 mt-0.5">View and manage your scheduled visits</p>
            </div>
            <a href="{{ route('appointments.index') }}"
               class="text-sm font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-xl transition">
                View →
            </a>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     ADMIN / STAFF VIEW
══════════════════════════════════════════════════════════════════════ --}}
@else

@php
    $departments  = \App\Models\Department::where('is_active', true)->get();
    $department   = request()->filled('department')
        ? \App\Models\Department::findOrFail(request('department'))
        : $departments->first();
    $stats        = $department
        ? app(\App\Services\QueueService::class)->getDailyStats($department)
        : ['total'=>0,'waiting'=>0,'serving'=>0,'done'=>0,'skipped'=>0,'current'=>null];
    $queues       = $department
        ? \App\Models\Queue::where('department_id', $department->id)
              ->whereDate('queue_date', today())
              ->with('patient')
              ->orderBy('queue_number')
              ->get()
        : collect();
    $patients     = \App\Models\Patient::whereNull('patients.deleted_at')
                        ->orderBy('first_name')->get();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LEFT: Stats + Actions --}}
    <div class="space-y-4">

        {{-- Now Serving card --}}
        <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-6 text-center shadow-xl relative overflow-hidden">
            <div class="absolute -top-6 -right-6 w-24 h-24 rounded-full bg-white/10"></div>
            <p class="text-blue-200 text-sm font-medium mb-2 uppercase tracking-widest relative z-10">Now Serving</p>
            <p id="admin-serving-number"
               class="text-white text-6xl font-display font-bold mb-1 relative z-10 transition-all duration-300">
                {{ $stats['current']?->queue_number ?? '—' }}
            </p>
            <p id="admin-serving-code"
               class="text-blue-200 text-sm relative z-10">
                {{ $stats['current']?->queue_code ?? 'No patient being served' }}
            </p>
            <p id="admin-serving-name"
               class="text-white font-medium mt-1 text-sm truncate px-4 relative z-10 {{ $stats['current'] ? '' : 'hidden' }}">
                {{ $stats['current']?->patient->full_name ?? '' }}
            </p>
        </div>

        {{-- Stats cards --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Today's Summary</h3>
                <div class="flex items-center gap-1.5 text-xs text-green-600">
                    <span id="admin-live-dot" class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span>Live</span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-amber-50 rounded-xl p-3 text-center">
                    <p id="admin-stat-waiting" class="text-2xl font-bold text-amber-700 transition-all duration-300">{{ $stats['waiting'] }}</p>
                    <p class="text-xs text-amber-600 font-medium">Waiting</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-3 text-center">
                    <p id="admin-stat-serving" class="text-2xl font-bold text-blue-700 transition-all duration-300">{{ $stats['serving'] }}</p>
                    <p class="text-xs text-blue-600 font-medium">Serving</p>
                </div>
                <div class="bg-green-50 rounded-xl p-3 text-center">
                    <p id="admin-stat-done" class="text-2xl font-bold text-green-700 transition-all duration-300">{{ $stats['done'] }}</p>
                    <p class="text-xs text-green-600 font-medium">Done</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3 text-center">
                    <p id="admin-stat-total" class="text-2xl font-bold text-slate-700 transition-all duration-300">{{ $stats['total'] }}</p>
                    <p class="text-xs text-slate-600 font-medium">Total</p>
                </div>
            </div>
        </div>

        {{-- Call Next --}}
        <button type="button" id="call-next-btn"
                data-url="{{ route('queue.call-next', $department) }}"
                class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-green-500/25 hover:-translate-y-0.5 flex items-center justify-center gap-2 text-base">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span id="call-next-label">Next Queue</span>
        </button>

        {{-- Add Walk-in --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">Add Walk-in Patient</h3>
            <form id="add-queue-form" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Patient</label>
                    <select name="patient_id" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Select patient...</option>
                        @foreach($patients as $pat)
                            <option value="{{ $pat->id }}">{{ $pat->full_name }} ({{ $pat->patient_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Department</label>
                    <select name="department_id" id="walkin-dept" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $dept->id == $department->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="button" id="add-queue-btn"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span id="add-queue-label">Add to Queue</span>
                </button>
            </form>
        </div>
    </div>

    {{-- RIGHT: Queue List --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

            {{-- Header with department switcher (NO form submit / page reload) --}}
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 id="queue-dept-title" class="font-semibold text-slate-800">
                        {{ $department->name }} Queue
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ now()->format('F j, Y') }}</p>
                </div>

                <div class="relative flex items-center gap-2">
                    {{-- Loading spinner (hidden by default) --}}
                    <svg id="dept-loading-spinner"
                         class="hidden w-4 h-4 text-blue-500 animate-spin"
                         fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>

                    {{-- Department selector — triggers AJAX, not page reload --}}
                    <select id="dept-select"
                            class="text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white cursor-pointer transition-all">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}"
                                    data-callnext="{{ route('queue.call-next', $dept) }}"
                                    {{ $dept->id == $department->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Queue list — updated via AJAX --}}
            <div id="admin-queue-list" class="transition-opacity duration-200">
                @include('queue._queue_rows', ['queues' => $queues])
            </div>

            <div class="px-5 py-3 bg-slate-50 border-t border-slate-100">
                <p class="text-xs text-slate-400 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Auto-updates every 5 seconds. Switching departments loads instantly without page refresh.
                </p>
            </div>
        </div>
    </div>
</div>

@endif

@push('scripts')
<script>
const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content;
const IS_PATIENT = {{ auth()->user()->isPatient() ? 'true' : 'false' }};
let   CURRENT_DEPT_ID = {{ isset($department) ? $department->id : 'null' }};
let   pollTimer = null;

// ── Animate number smoothly ────────────────────────────────────────────────
function animateUpdate(el, newVal) {
    if (!el || el.textContent.trim() === String(newVal)) return;
    el.style.transform = 'scale(1.12)';
    el.style.opacity   = '0.3';
    setTimeout(() => {
        el.textContent     = newVal;
        el.style.transform = 'scale(1)';
        el.style.opacity   = '1';
    }, 180);
}

// ── Flash live indicator ───────────────────────────────────────────────────
function flashLive(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('bg-blue-400');
    setTimeout(() => el.classList.remove('bg-blue-400'), 500);
}

// ── Show inline flash message ──────────────────────────────────────────────
function showFlash(message, type = 'success') {
    document.getElementById('ajax-flash')?.remove();
    const colors = {
        success: 'bg-green-50 border-green-200 text-green-700',
        error:   'bg-red-50 border-red-200 text-red-700',
        info:    'bg-blue-50 border-blue-200 text-blue-700',
    };
    const div       = document.createElement('div');
    div.id          = 'ajax-flash';
    div.className   = `fixed top-5 right-5 z-50 flex items-center gap-3 border px-4 py-3 rounded-xl text-sm shadow-lg max-w-sm ${colors[type]}`;
    div.innerHTML   = `<span class="flex-1">${message}</span>
                       <button onclick="this.parentElement.remove()" class="opacity-60 hover:opacity-100 text-lg leading-none">✕</button>`;
    document.body.appendChild(div);
    setTimeout(() => div?.remove(), 4000);
}

// ── Render queue rows from poll data ──────────────────────────────────────
function renderQueueRows(queues) {
    const list = document.getElementById('admin-queue-list');
    if (!list) return;

    if (!queues || queues.length === 0) {
        list.innerHTML = `
            <div class="text-center py-16">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-slate-500 font-medium">No patients in queue today</p>
            </div>`;
        return;
    }

    list.innerHTML = `<div class="divide-y divide-slate-50">` + queues.map(q => {
        const isServing = q.status === 'serving';
        const isSkipped = q.status === 'skipped';
        const numBg     = isServing ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30'
                        : isSkipped ? 'bg-slate-200 text-slate-400'
                        : 'bg-slate-100 text-slate-600';
        const rowClass  = isServing ? 'bg-blue-50/60 border-l-4 border-l-blue-500' : '';
        const rowOp     = isSkipped ? 'opacity-50' : '';
        const badgeMap  = { waiting:'badge-waiting', serving:'badge-serving', done:'badge-done', skipped:'badge-skipped' };
        const badge     = badgeMap[q.status] || 'badge-waiting';
        const nowLabel  = isServing
            ? `<span class="flex items-center gap-1 text-xs text-blue-600 font-medium">
                   <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>Now Serving
               </span>` : '';
        const skipBtn   = q.status === 'waiting'
            ? `<form method="POST" action="/queue/${q.id}"
                    onsubmit="return confirm('Skip ${q.patient_name.replace(/'/g,"\\'")}?')">
                   <input type="hidden" name="_token" value="${CSRF}">
                   <input type="hidden" name="_method" value="DELETE">
                   <button type="submit"
                           class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-orange-500 hover:bg-orange-50 transition-all"
                           title="Skip patient">
                       <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                 d="M11.933 12.8a1 1 0 000-1.6L6.6 7.2A1 1 0 005 8v8a1 1 0 001.6.8l5.333-4zM19.933 12.8a1 1 0 000-1.6l-5.333-4A1 1 0 0013 8v8a1 1 0 001.6.8l5.333-4z"/>
                       </svg>
                   </button>
               </form>` : '';

        return `
            <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 transition-colors ${rowClass} ${rowOp}">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 font-bold text-lg ${numBg}">
                    ${q.queue_number}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="text-sm font-semibold text-slate-800 truncate">${q.patient_name}</p>
                        ${nowLabel}
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        ${q.queue_code}${q.called_at ? ' · Called ' + q.called_at : ''}
                    </p>
                </div>
                <span class="${badge} text-xs font-semibold px-3 py-1 rounded-full flex-shrink-0 capitalize">
                    ${q.status}
                </span>
                ${skipBtn}
            </div>`;
    }).join('') + `</div>`;
}

// ══════════════════════════════════════════════════════════════════════════
//  ADMIN POLL — fetches queue data for current department
// ══════════════════════════════════════════════════════════════════════════
async function pollAdmin(showLoader = false) {
    if (!CURRENT_DEPT_ID) return;
    try {
        const res  = await fetch(`/queue/poll/admin?department_id=${CURRENT_DEPT_ID}`, {
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        if (!res.ok) return;
        const data = await res.json();

        // Stats
        animateUpdate(document.getElementById('admin-stat-waiting'), data.stats.waiting);
        animateUpdate(document.getElementById('admin-stat-serving'), data.stats.serving);
        animateUpdate(document.getElementById('admin-stat-done'),    data.stats.done);
        animateUpdate(document.getElementById('admin-stat-total'),   data.stats.total);
        flashLive('admin-live-dot');

        // Now serving
        const curr     = data.current;
        const numEl    = document.getElementById('admin-serving-number');
        const codeEl   = document.getElementById('admin-serving-code');
        const nameEl   = document.getElementById('admin-serving-name');
        if (curr) {
            animateUpdate(numEl, curr.queue_number);
            if (codeEl) codeEl.textContent = curr.queue_code;
            if (nameEl) { nameEl.textContent = curr.patient_name; nameEl.classList.remove('hidden'); }
        } else {
            animateUpdate(numEl, '—');
            if (codeEl) codeEl.textContent = 'No patient being served';
            if (nameEl) nameEl.classList.add('hidden');
        }

        // Queue rows
        if (showLoader) {
            const list = document.getElementById('admin-queue-list');
            if (list) list.style.opacity = '1';
            const spinner = document.getElementById('dept-loading-spinner');
            if (spinner) spinner.classList.add('hidden');
        }
        renderQueueRows(data.queues);

    } catch (e) {
        console.warn('Admin poll error:', e);
        if (showLoader) {
            const spinner = document.getElementById('dept-loading-spinner');
            if (spinner) spinner.classList.add('hidden');
        }
    }
}

// ══════════════════════════════════════════════════════════════════════════
//  DEPARTMENT SWITCHER — AJAX, no page reload
// ══════════════════════════════════════════════════════════════════════════
const deptSelect = document.getElementById('dept-select');
if (deptSelect) {
    deptSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        CURRENT_DEPT_ID      = parseInt(this.value);

        // Update the department title
        const titleEl = document.getElementById('queue-dept-title');
        if (titleEl) titleEl.textContent = selectedOption.text + ' Queue';

        // Update Call Next button URL
        const callNextBtn = document.getElementById('call-next-btn');
        if (callNextBtn && selectedOption.dataset.callnext) {
            callNextBtn.dataset.url = selectedOption.dataset.callnext;
        }

        // Update walk-in department selector to match
        const walkinDept = document.getElementById('walkin-dept');
        if (walkinDept) walkinDept.value = CURRENT_DEPT_ID;

        // Show loading state
        const list    = document.getElementById('admin-queue-list');
        const spinner = document.getElementById('dept-loading-spinner');
        if (list)    list.style.opacity = '0.4';
        if (spinner) spinner.classList.remove('hidden');

        // Fetch new data immediately
        pollAdmin(true);

        // Restart polling timer for new department
        clearInterval(pollTimer);
        pollTimer = setInterval(() => pollAdmin(), 5000);
    });
}

// ══════════════════════════════════════════════════════════════════════════
//  CALL NEXT — AJAX
// ══════════════════════════════════════════════════════════════════════════
const callNextBtn = document.getElementById('call-next-btn');
if (callNextBtn) {
    callNextBtn.addEventListener('click', async function () {
        const url   = this.dataset.url;
        const label = document.getElementById('call-next-label');
        label.textContent    = 'Calling...';
        callNextBtn.disabled = true;
        try {
            const res = await fetch(url, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const ct = res.headers.get('content-type');
            if (ct && ct.includes('application/json')) {
                const data = await res.json();
                showFlash(data.message || 'Next patient called.', data.status === 'info' ? 'info' : 'success');
            } else {
                showFlash('Next patient called successfully.', 'success');
            }
            pollAdmin();
        } catch (e) {
            showFlash('Next patient called.', 'success');
            pollAdmin();
        } finally {
            label.textContent    = 'Call Next Patient';
            callNextBtn.disabled = false;
        }
    });
}

// ══════════════════════════════════════════════════════════════════════════
//  ADD WALK-IN — AJAX
// ══════════════════════════════════════════════════════════════════════════
const addQueueBtn = document.getElementById('add-queue-btn');
if (addQueueBtn) {
    addQueueBtn.addEventListener('click', async function () {
        const form     = document.getElementById('add-queue-form');
        const label    = document.getElementById('add-queue-label');
        const formData = new FormData(form);
        if (!formData.get('patient_id') || !formData.get('department_id')) {
            showFlash('Please select a patient and department.', 'error');
            return;
        }
        label.textContent    = 'Adding...';
        addQueueBtn.disabled = true;
        try {
            const res = await fetch('{{ route("queue.join") }}', {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body:    formData,
            });
            const ct = res.headers.get('content-type');
            if (ct && ct.includes('application/json')) {
                const data = await res.json();
                if (data.error) { showFlash(data.error, 'error'); }
                else { showFlash(data.message || 'Patient added to queue.', 'success'); form.reset(); pollAdmin(); }
            } else {
                showFlash('Patient added to queue successfully.', 'success');
                form.reset();
                pollAdmin();
            }
        } catch (e) {
            showFlash('Patient added to queue successfully.', 'success');
            form.reset();
            pollAdmin();
        } finally {
            label.textContent    = 'Add to Queue';
            addQueueBtn.disabled = false;
        }
    });
}

// ══════════════════════════════════════════════════════════════════════════
//  PATIENT POLL
// ══════════════════════════════════════════════════════════════════════════
if (IS_PATIENT) {
    async function pollPatient() {
        try {
            const res  = await fetch('/queue/poll/patient', {
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            animateUpdate(document.getElementById('stat-waiting'), data.stats.waiting);
            animateUpdate(document.getElementById('stat-serving'), data.stats.serving);
            animateUpdate(document.getElementById('stat-done'),    data.stats.done);
            flashLive('live-dot');

            const list = document.getElementById('now-serving-list');
            if (list && data.now_serving) {
                list.innerHTML = data.now_serving.length === 0
                    ? `<div class="text-center py-8 text-slate-400 text-sm">No patients being served right now</div>`
                    : data.now_serving.map(s => `
                        <div class="flex items-center gap-4 px-5 py-3.5 border-b border-slate-50 last:border-0">
                            <div class="w-12 h-12 rounded-xl bg-green-600 text-white flex items-center justify-center font-bold text-xl flex-shrink-0">${s.queue_number}</div>
                            <div><p class="text-sm font-semibold text-slate-800">${s.queue_code}</p><p class="text-xs text-slate-500">${s.department_name}</p></div>
                            <span class="ml-auto text-xs font-semibold text-green-600 bg-green-50 px-2.5 py-1 rounded-full">Serving</span>
                        </div>`).join('');
            }

            const myQ       = data.my_queue;
            const aheadText = document.getElementById('my-queue-ahead-text');
            if (myQ && aheadText) {
                animateUpdate(document.getElementById('my-queue-number'), myQ.queue_number);
                aheadText.textContent = myQ.ahead > 0 ? `${myQ.ahead} patient(s) ahead of you` : "You're next!";
            }
        } catch (e) { console.warn('Patient poll error:', e); }
    }
    setInterval(pollPatient, 5000);
}

// Start admin polling
if (!IS_PATIENT && CURRENT_DEPT_ID) {
    pollTimer = setInterval(() => pollAdmin(), 5000);
}
</script>
@endpush

@endsection
