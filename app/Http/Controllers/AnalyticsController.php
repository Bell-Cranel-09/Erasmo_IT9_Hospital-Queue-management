<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Support\Carbon;

class AnalyticsController extends Controller
{
    public function weeklyQueue()
    {
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek   = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $date    = $startOfWeek->copy()->addDays($i);
            $count   = Queue::whereDate('queue_date', $date->format('Y-m-d'))->count();
            $days[]  = [
                'date'     => $date->format('Y-m-d'),
                'day'      => $date->format('D'),
                'day_full' => $date->format('l'),
                'count'    => $count,
                'is_today' => $date->isToday(),
            ];
        }

        $total   = array_sum(array_column($days, 'count'));
        $peak    = max(array_column($days, 'count'));
        $peakDay = collect($days)->firstWhere('count', $peak);

        return response()->json([
            'days'     => $days,
            'total'    => $total,
            'peak'     => $peak,
            'peak_day' => $peakDay['day_full'] ?? '—',
            'week'     => $startOfWeek->format('M j') . ' – ' . $endOfWeek->format('M j, Y'),
        ]);
    }
}
