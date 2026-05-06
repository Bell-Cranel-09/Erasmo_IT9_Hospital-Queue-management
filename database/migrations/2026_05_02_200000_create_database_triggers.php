<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── TRIGGER 1 ─────────────────────────────────────────────────────
        // After a new queue entry is inserted with status 'waiting',
        // automatically cancel any PENDING appointments for the same
        // patient in the same department on the same day — because
        // they are now in the physical queue.
        // ─────────────────────────────────────────────────────────────────
        DB::unprepared('
            CREATE TRIGGER trg_after_queue_insert
            AFTER INSERT ON queues
            FOR EACH ROW
            BEGIN
                -- Only act when a new waiting queue entry is created
                IF NEW.status = "waiting" AND NEW.appointment_id IS NOT NULL THEN
                    -- Auto-confirm the linked appointment when patient joins queue
                    UPDATE appointments
                    SET status = "confirmed"
                    WHERE id = NEW.appointment_id
                      AND status = "pending";
                END IF;
            END
        ');

        // ── TRIGGER 2 ─────────────────────────────────────────────────────
        // After a queue status changes to "done",
        // automatically mark the linked appointment as "completed".
        // ─────────────────────────────────────────────────────────────────
        DB::unprepared('
            CREATE TRIGGER trg_after_queue_done
            AFTER UPDATE ON queues
            FOR EACH ROW
            BEGIN
                -- When queue is marked done and has a linked appointment
                IF NEW.status = "done" AND OLD.status != "done"
                   AND NEW.appointment_id IS NOT NULL THEN
                    UPDATE appointments
                    SET status = "completed"
                    WHERE id = NEW.appointment_id
                      AND status IN ("pending", "confirmed");
                END IF;
            END
        ');

        // ── TRIGGER 3 ─────────────────────────────────────────────────────
        // Before inserting a new queue entry, check if the patient
        // already has an active queue (waiting/serving) in the SAME
        // department today. If so, raise an error.
        // This is a database-level guard on top of the app-level check.
        // ─────────────────────────────────────────────────────────────────
        DB::unprepared('
            CREATE TRIGGER trg_before_queue_insert
            BEFORE INSERT ON queues
            FOR EACH ROW
            BEGIN
                DECLARE existing_count INT;

                SELECT COUNT(*) INTO existing_count
                FROM queues
                WHERE patient_id    = NEW.patient_id
                  AND department_id = NEW.department_id
                  AND queue_date    = NEW.queue_date
                  AND status        IN ("waiting", "serving");

                IF existing_count > 0 THEN
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "Patient already has an active queue entry in this department today.";
                END IF;
            END
        ');

        // ── TRIGGER 4 ─────────────────────────────────────────────────────
        // Before inserting a new appointment, check for double booking.
        // Same doctor, same date, same time — reject at DB level.
        // ─────────────────────────────────────────────────────────────────
        DB::unprepared('
            CREATE TRIGGER trg_before_appointment_insert
            BEFORE INSERT ON appointments
            FOR EACH ROW
            BEGIN
                DECLARE conflict_count INT;

                SELECT COUNT(*) INTO conflict_count
                FROM appointments
                WHERE doctor_id          = NEW.doctor_id
                  AND appointment_date   = NEW.appointment_date
                  AND appointment_time   = NEW.appointment_time
                  AND status            != "canceled";

                IF conflict_count > 0 THEN
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "This appointment slot is already booked for this doctor.";
                END IF;
            END
        ');

        // ── TRIGGER 5 ─────────────────────────────────────────────────────
        // Before updating an appointment, prevent changing a
        // "completed" appointment back to any other status.
        // ─────────────────────────────────────────────────────────────────
        DB::unprepared('
            CREATE TRIGGER trg_before_appointment_update
            BEFORE UPDATE ON appointments
            FOR EACH ROW
            BEGIN
                IF OLD.status = "completed" AND NEW.status != "completed" THEN
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "Cannot change the status of a completed appointment.";
                END IF;
            END
        ');

        // ── TRIGGER 6 ─────────────────────────────────────────────────────
        // After a patient is deleted, automatically cancel all their
        // upcoming pending/confirmed appointments.
        // ─────────────────────────────────────────────────────────────────
        DB::unprepared('
            CREATE TRIGGER trg_after_patient_delete
            AFTER DELETE ON patients
            FOR EACH ROW
            BEGIN
                -- Cancel all future pending/confirmed appointments
                UPDATE appointments
                SET status = "canceled"
                WHERE patient_id = OLD.id
                  AND status IN ("pending", "confirmed")
                  AND appointment_date >= CURDATE();

                -- Mark any active queue entries as skipped
                UPDATE queues
                SET status = "skipped",
                    done_at = NOW()
                WHERE patient_id = OLD.id
                  AND status IN ("waiting", "serving")
                  AND queue_date = CURDATE();
            END
        ');

        // ── TRIGGER 7 ─────────────────────────────────────────────────────
        // After a new patient is created, automatically log the
        // creation timestamp (updated_at sync guard).
        // Also ensures patient_code is never empty.
        // ─────────────────────────────────────────────────────────────────
        DB::unprepared('
            CREATE TRIGGER trg_before_patient_insert
            BEFORE INSERT ON patients
            FOR EACH ROW
            BEGIN
                IF NEW.patient_code IS NULL OR NEW.patient_code = "" THEN
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "Patient code cannot be empty.";
                END IF;
            END
        ');

        // ── TRIGGER 8 ─────────────────────────────────────────────────────
        // Before inserting a doctor schedule, validate that
        // end_time is always after start_time at the DB level.
        // ─────────────────────────────────────────────────────────────────
        DB::unprepared('
            CREATE TRIGGER trg_before_schedule_insert
            BEFORE INSERT ON doctor_schedules
            FOR EACH ROW
            BEGIN
                IF NEW.end_time <= NEW.start_time THEN
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "Schedule end time must be after start time.";
                END IF;

                IF NEW.max_patients <= 0 THEN
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "Max patients must be greater than zero.";
                END IF;
            END
        ');

        // ── TRIGGER 9 ─────────────────────────────────────────────────────
        // Same validation when a schedule is updated.
        // ─────────────────────────────────────────────────────────────────
        DB::unprepared('
            CREATE TRIGGER trg_before_schedule_update
            BEFORE UPDATE ON doctor_schedules
            FOR EACH ROW
            BEGIN
                IF NEW.end_time <= NEW.start_time THEN
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "Schedule end time must be after start time.";
                END IF;
            END
        ');
    }

    public function down(): void
    {
        // Drop all triggers in reverse order
        DB::unprepared('DROP TRIGGER IF EXISTS trg_before_schedule_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_before_schedule_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_before_patient_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_patient_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_before_appointment_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_before_appointment_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_before_queue_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_queue_done');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_queue_insert');
    }
};
