<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Display – {{ $department->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@300;400;700;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans:    ['DM Sans', 'sans-serif'],
                        display: ['Instrument Serif', 'serif'],
                    },
                    animation: {
                        'pulse-number': 'pulseNumber 2s ease-in-out infinite',
                        'blink': 'blink 1s step-end infinite',
                    },
                    keyframes: {
                        pulseNumber: {
                            '0%,100%': { transform: 'scale(1)', opacity: 1 },
                            '50%': { transform: 'scale(1.04)', opacity: 0.9 },
                        },
                        blink: { '0%,100%': { opacity: 1 }, '50%': { opacity: 0 } },
                    }
                }
            }
        }
    </script>
    <style>
        body { background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #1e40af 100%); min-height: 100vh; }
        .dot-pattern { background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px); background-size: 28px 28px; }
    </style>
</head>
<body class="font-sans dot-pattern flex flex-col min-h-screen text-white">

    {{-- Header --}}
    <header class="px-10 py-6 flex items-center justify-between border-b border-white/10">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-blue-200 text-sm font-medium">St. Gabriel Medical Center</p>
                <p class="text-white font-bold text-lg">{{ $department->name }} Department</p>
            </div>
        </div>
        <div class="text-right">
            <p id="clock" class="text-4xl font-mono font-bold text-white"></p>
            <p class="text-blue-300 text-sm">{{ now()->format('F j, Y') }}</p>
        </div>
    </header>

    {{-- Main display --}}
    <main class="flex-1 flex items-center justify-center px-10 py-8">
        <div class="w-full max-w-5xl">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- NOW SERVING --}}
                <div class="bg-white/10 backdrop-blur rounded-3xl p-10 text-center border border-white/20 shadow-2xl">
                    <p class="text-blue-200 text-sm font-semibold uppercase tracking-[0.3em] mb-4">Now Serving</p>

                    @if($current)
                        <div class="animate-pulse-number">
                            <p class="text-[9rem] font-display font-bold text-white leading-none">
                                {{ $current->queue_number }}
                            </p>
                            <p class="text-blue-300 text-2xl font-semibold mt-1">{{ $current->queue_code }}</p>
                        </div>
                        <div class="mt-6 bg-white/10 rounded-2xl px-6 py-4">
                            <p class="text-white font-semibold text-xl">{{ $current->patient->full_name }}</p>
                            @if($current->called_at)
                                <p class="text-blue-300 text-sm mt-1">Called at {{ $current->called_at->format('h:i A') }}</p>
                            @endif
                        </div>
                    @else
                        <div class="py-8">
                            <p class="text-[6rem] font-display text-white/30 leading-none">—</p>
                            <p class="text-blue-300 text-lg mt-4">Waiting for next patient</p>
                        </div>
                    @endif
                </div>

                {{-- WAITING LIST --}}
                <div class="bg-white/10 backdrop-blur rounded-3xl p-8 border border-white/20 shadow-2xl">
                    <p class="text-blue-200 text-sm font-semibold uppercase tracking-[0.3em] mb-5">
                        Up Next
                        @if($waiting->count() > 0)
                            <span class="ml-2 bg-white/20 text-white px-2.5 py-0.5 rounded-full text-xs">{{ $waiting->count() }}</span>
                        @endif
                    </p>

                    @if($waiting->isEmpty())
                        <div class="flex items-center justify-center h-40">
                            <p class="text-blue-300 text-lg">No patients waiting</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($waiting->take(6) as $queue)
                            <div class="flex items-center gap-4 bg-white/10 rounded-2xl px-5 py-3">
                                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center font-bold text-xl text-white flex-shrink-0">
                                    {{ $queue->queue_number }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-semibold text-base truncate">{{ $queue->patient->full_name }}</p>
                                    <p class="text-blue-300 text-sm">{{ $queue->queue_code }}</p>
                                </div>
                            </div>
                            @endforeach

                            @if($waiting->count() > 6)
                                <p class="text-center text-blue-300 text-sm pt-2">
                                    +{{ $waiting->count() - 6 }} more patients waiting
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="px-10 py-4 border-t border-white/10 flex items-center justify-between">
        <p class="text-blue-300 text-sm">Please proceed to the counter when your number is called.</p>
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
            <span class="text-blue-300 text-sm">Live Display</span>
        </div>
    </footer>

    <script>
        // Live clock
        function updateClock() {
            document.getElementById('clock').textContent =
                new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Auto-refresh every 15 seconds to show latest queue state
        setInterval(() => location.reload(), 15000);
    </script>
</body>
</html>
