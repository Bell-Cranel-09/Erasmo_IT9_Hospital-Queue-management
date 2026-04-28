<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Patient;
use App\Models\Queue;
use Illuminate\Support\Facades\DB;

class QueueService
{
    /**
     * Register a patient into a department queue.
     * Handles walk-ins and appointment-based entries.
     */
    public function joinQueue(Patient $patient, Department $department, ?int $appointmentId = null): Queue
    {
        // Prevent joining the same department queue twice today
        $existing = Queue::where('patient_id', $patient->id)
            ->where('department_id', $department->id)
            ->whereDate('queue_date', today())
            ->whereIn('status', ['waiting', 'serving'])
            ->first();

        if ($existing) {
            throw new \Exception("Patient is already in this department's queue today.");
        }

        return DB::transaction(function () use ($patient, $department, $appointmentId) {
            // Get next queue number for this department today
            $lastNumber = Queue::where('department_id', $department->id)
                ->whereDate('queue_date', today())
                ->lockForUpdate()          // Prevent race conditions
                ->max('queue_number') ?? 0;

            $nextNumber = $lastNumber + 1;

            // Format: "GEN-001", "PED-005", etc.
            $queueCode = $department->code . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            return Queue::create([
                'patient_id'     => $patient->id,
                'department_id'  => $department->id,
                'appointment_id' => $appointmentId,
                'queue_number'   => $nextNumber,
                'queue_code'     => $queueCode,
                'queue_date'     => today(),
                'status'         => 'waiting',
            ]);
        });
    }

    /**
     * Call the next waiting patient in a department.
     * Marks the current "serving" patient as done first.
     */
    public function callNext(Department $department): ?Queue
    {
        return DB::transaction(function () use ($department) {
            // Mark whoever is currently being served as done
            Queue::where('department_id', $department->id)
                ->whereDate('queue_date', today())
                ->where('status', 'serving')
                ->update([
                    'status'  => 'done',
                    'done_at' => now(),
                ]);

            // Get the next waiting patient
            $next = Queue::where('department_id', $department->id)
                ->whereDate('queue_date', today())
                ->where('status', 'waiting')
                ->orderBy('queue_number')
                ->lockForUpdate()
                ->first();

            if ($next) {
                $next->update([
                    'status'    => 'serving',
                    'called_at' => now(),
                    'served_at' => now(),
                ]);
            }

            return $next;
        });
    }

    /**
     * Get today's queue summary for a department.
     */
    public function getDailyStats(Department $department): array
    {
        $queues = Queue::where('department_id', $department->id)
            ->whereDate('queue_date', today())
            ->get();

        return [
            'total'    => $queues->count(),
            'waiting'  => $queues->where('status', 'waiting')->count(),
            'serving'  => $queues->where('status', 'serving')->count(),
            'done'     => $queues->where('status', 'done')->count(),
            'skipped'  => $queues->where('status', 'skipped')->count(),
            'current'  => $queues->firstWhere('status', 'serving'),
        ];
    }
}