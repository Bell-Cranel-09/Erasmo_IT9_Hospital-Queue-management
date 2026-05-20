{{--
    resources/views/queue/_queue_rows.blade.php
    This is a shared partial used for the initial server render of the queue list.
    After that, AJAX polling takes over and renders rows via JavaScript.
--}}

@if($queues->isEmpty())
<div class="text-center py-16">
    <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <p class="text-slate-500 font-medium">No patients in queue today</p>
</div>
@else
<div class="divide-y divide-slate-50">
    @foreach($queues as $queue)
    <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 transition-colors
                {{ $queue->status === 'serving' ? 'bg-blue-50/60 border-l-4 border-l-blue-500' : '' }}
                {{ $queue->status === 'skipped' ? 'opacity-50' : '' }}">

        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 font-bold text-lg
                    {{ $queue->status === 'serving' ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' :
                       ($queue->status === 'skipped' ? 'bg-slate-200 text-slate-400' : 'bg-slate-100 text-slate-600') }}">
            {{ $queue->queue_number }}
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <p class="text-sm font-semibold text-slate-800 truncate">
                    {{ $queue->patient->full_name }}
                </p>
                @if($queue->status === 'serving')
                    <span class="flex items-center gap-1 text-xs text-blue-600 font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                        Now Serving
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ $queue->queue_code }}
                @if($queue->called_at) · Called {{ $queue->called_at->format('h:i A') }} @endif
                @if($queue->status === 'skipped') · <span class="text-orange-500">Skipped</span> @endif
            </p>
        </div>

        <span class="badge-{{ $queue->status }} text-xs font-semibold px-3 py-1 rounded-full flex-shrink-0 capitalize">
            {{ $queue->status }}
        </span>

        @if($queue->status === 'waiting')
        <form method="POST" action="{{ route('queue.destroy', $queue) }}"
              onsubmit="return confirm('Skip {{ $queue->patient->full_name }}? Record will be preserved.')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-orange-500 hover:bg-orange-50 transition-all"
                    title="Skip patient">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11.933 12.8a1 1 0 000-1.6L6.6 7.2A1 1 0 005 8v8a1 1 0 001.6.8l5.333-4zM19.933 12.8a1 1 0 000-1.6l-5.333-4A1 1 0 0013 8v8a1 1 0 001.6.8l5.333-4z"/>
                </svg>
            </button>
        </form>
        @endif
    </div>
    @endforeach
</div>
@endif
