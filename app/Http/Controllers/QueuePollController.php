<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Queue;
use Illuminate\Http\Request;

class QueuePollController extends Controller
{
    /**
     * AJAX polling endpoint for admin/staff queue updates.
     * Returns JSON with current queue state for a specific department.
     */
    public function adminPoll(Request $request)
    {
        $departmentId = $request->get('department_id');

        $department = $departmentId
            ? Department::find($departmentId)
            : Department::where('is_active', true)->first();

        if (!$department) {
            return response()->json(['error' => 'No department found'], 404);
        }

        $queues = Queue::where('department_id', $department->id)
            ->whereDate('queue_date', today())
            ->with('patient:id,first_name,last_name,patient_code')
            ->orderBy('queue_number')
            ->get()
            ->map(fn($q) => [
                'id'           => $q->id,
                'queue_number' => $q->queue_number,
                'queue_code'   => $q->queue_code,
                'status'       => $q->status,
                'patient_name' => $q->patient->full_name,
                'patient_code' => $q->patient->patient_code,
                'called_at'    => $q->called_at?->format('h:i A'),
            ]);

        $current = $queues->firstWhere('status', 'serving');

        return response()->json([
            'current'   => $current,
            'queues'    => $queues,
            'stats'     => [
                'waiting' => $queues->where('status', 'waiting')->count(),
                'serving' => $queues->where('status', 'serving')->count(),
                'done'    => $queues->where('status', 'done')->count(),
                'skipped' => $queues->where('status', 'skipped')->count(),
                'total'   => $queues->count(),
            ],
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * AJAX polling endpoint for patient queue view.
     * Returns combined stats across all departments + patient's own queue position.
     */
    public function patientPoll(Request $request)
    {
        $patientId = auth()->user()->patient?->id;

        // My active queue entry
        $myQueue = null;
        if ($patientId) {
            $myQueueRecord = Queue::where('patient_id', $patientId)
                ->whereDate('queue_date', today())
                ->whereIn('status', ['waiting', 'serving'])
                ->with('department:id,name,code')
                ->first();

            if ($myQueueRecord) {
                // Count how many are ahead of this patient in the same dept
                $ahead = Queue::where('department_id', $myQueueRecord->department_id)
                    ->whereDate('queue_date', today())
                    ->where('status', 'waiting')
                    ->where('queue_number', '<', $myQueueRecord->queue_number)
                    ->count();

                $myQueue = [
                    'queue_number'    => $myQueueRecord->queue_number,
                    'queue_code'      => $myQueueRecord->queue_code,
                    'status'          => $myQueueRecord->status,
                    'department_name' => $myQueueRecord->department->name,
                    'ahead'           => $ahead,
                ];
            }
        }

        // Overall stats across ALL departments
        $allQueues = Queue::whereDate('queue_date', today())->get();

        // Currently serving in each department
        $nowServing = Queue::whereDate('queue_date', today())
            ->where('status', 'serving')
            ->with('department:id,name')
            ->get()
            ->map(fn($q) => [
                'queue_number'    => $q->queue_number,
                'queue_code'      => $q->queue_code,
                'department_name' => $q->department->name,
            ]);

        return response()->json([
            'my_queue'   => $myQueue,
            'now_serving'=> $nowServing,
            'stats'      => [
                'waiting' => $allQueues->where('status', 'waiting')->count(),
                'serving' => $allQueues->where('status', 'serving')->count(),
                'done'    => $allQueues->where('status', 'done')->count(),
                'total'   => $allQueues->count(),
            ],
            'timestamp'  => now()->toISOString(),
        ]);
    }
}
