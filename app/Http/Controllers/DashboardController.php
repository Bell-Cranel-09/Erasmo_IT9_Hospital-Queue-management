<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Patient;
use App\Models\Queue;
use App\Models\Appointment;

class DashboardController extends Controller
{
    public function index()
    {
        // Summary stats
        $totalPatients     = Patient::count();
        $todayAppointments = Appointment::whereDate('appointment_date', today())->count();
        $waitingCount      = Queue::whereDate('queue_date', today())->where('status', 'waiting')->count();

        // Currently serving (first department for simplicity; adapt as needed)
        $currentQueue = Queue::whereDate('queue_date', today())
            ->where('status', 'serving')
            ->with('patient', 'department')
            ->first();

        // Latest 6 queue entries today for the preview widget
        $recentQueues = Queue::whereDate('queue_date', today())
            ->with(['patient', 'department'])
            ->orderByDesc('queue_number')
            ->take(6)
            ->get();

        return view('dashboard.index', compact(
            'totalPatients',
            'todayAppointments',
            'waitingCount',
            'currentQueue',
            'recentQueues'
        ));
    }
}
