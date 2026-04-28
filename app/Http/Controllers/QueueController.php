<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Patient;
use App\Models\Queue;
use App\Services\QueueService;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function __construct(protected QueueService $queueService) {}

    public function index(Request $request)
    {
        $departments = Department::where('is_active', true)->get();

        $department = $request->filled('department')
            ? Department::findOrFail($request->department)
            : $departments->first();

        if (!$department) {
            return view('queue.index', [
                'departments' => $departments,
                'department'  => null,
                'queues'      => collect(),
                'stats'       => ['total'=>0,'waiting'=>0,'serving'=>0,'done'=>0,'skipped'=>0,'current'=>null],
                'patients'    => Patient::orderBy('first_name')->get(),
            ]);
        }

        $stats  = $this->queueService->getDailyStats($department);
        $queues = Queue::where('department_id', $department->id)
            ->whereDate('queue_date', today())
            ->with('patient')
            ->orderBy('queue_number')
            ->get();

        $patients = Patient::orderBy('first_name')->get();

        return view('queue.index', compact('departments', 'department', 'queues', 'stats', 'patients'));
    }

    public function join(Request $request)
    {
        $data = $request->validate([
            'patient_id'    => 'required|exists:patients,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        try {
            $queue = $this->queueService->joinQueue(
                Patient::findOrFail($data['patient_id']),
                Department::findOrFail($data['department_id'])
            );
            return back()->with('success', "Queue number assigned: {$queue->queue_code}");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function callNext(Department $department)
    {
        try {
            $next = $this->queueService->callNext($department);
            if ($next) {
                return back()->with('success', "Now serving: {$next->queue_code} — {$next->patient->full_name}");
            }
            return back()->with('info', 'No more patients waiting in this department.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function display(Department $department)
    {
        $current = $department->currentlyServing();
        $waiting = $department->todayWaitingQueues()->with('patient')->get();

        return view('queue.display', compact('department', 'current', 'waiting'));
    }

    /**
     * Remove a patient from the queue (admin/staff only).
     */
    public function destroy(Queue $queue)
    {
        // Only admin and staff can delete queue entries
        if (!auth()->user()->isAdmin() && !auth()->user()->isStaff()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $code = $queue->queue_code;
        $queue->delete();

        return back()->with('success', "Queue entry {$code} has been removed.");
    }
}
