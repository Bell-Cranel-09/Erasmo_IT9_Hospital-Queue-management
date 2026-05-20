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
                'patients'    => Patient::whereNull('deleted_at')->orderBy('first_name')->get(),
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
        if (!auth()->user()->isAdmin() && !auth()->user()->isStaff()) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Unauthorized.'], 403);
            }
            return back()->with('error', 'Unauthorized.');
        }

        $data = $request->validate([
            'patient_id'    => 'required|exists:patients,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        try {
            $queue = $this->queueService->joinQueue(
                Patient::findOrFail($data['patient_id']),
                Department::findOrFail($data['department_id'])
            );

            $message = "Queue number assigned: {$queue->queue_code}";

            if (request()->expectsJson()) {
                return response()->json([
                    'message'      => $message,
                    'queue_code'   => $queue->queue_code,
                    'queue_number' => $queue->queue_number,
                ]);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }
    }

   public function callNext(Department $department)
    {
    if (!auth()->user()->isAdmin() && !auth()->user()->isStaff()) {
        return request()->expectsJson()
            ? response()->json(['error' => 'Unauthorized.'], 403)
            : back()->with('error', 'Unauthorized.');
    }

    try {
        $next    = $this->queueService->callNext($department);
        $message = $next
            ? "Now serving: {$next->queue_code} — {$next->patient->full_name}"
            : 'No more patients waiting.';
        $status  = $next ? 'success' : 'info';

        return request()->expectsJson()
            ? response()->json(['message' => $message, 'status' => $status])
            : back()->with($status, $message);

    } catch (\Exception $e) {
        return request()->expectsJson()
            ? response()->json(['error' => $e->getMessage()], 422)
            : back()->with('error', $e->getMessage());
    }
    }

    public function display(Department $department)
    {
        $current = $department->currentlyServing();
        $waiting = $department->todayWaitingQueues()->with('patient')->get();

        return view('queue.display', compact('department', 'current', 'waiting'));
    }

    /**
     * SOFT DELETE — marks queue entry as "skipped", data is NOT deleted.
     * The queue record stays in the database for reporting/audit purposes.
     */
    public function destroy(Queue $queue)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isStaff()) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Mark as skipped — does NOT delete the record
        $queue->update([
            'status'  => 'skipped',
            'done_at' => now(),
        ]);

        return back()->with('success', "Queue {$queue->queue_code} ({$queue->patient->full_name}) marked as skipped. Record preserved.");
    }
}
