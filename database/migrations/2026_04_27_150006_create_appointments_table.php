<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_schedule_id')->constrained()->onDelete('restrict');

            $table->date('appointment_date');
            $table->time('appointment_time');        // The actual booked time slot

            $table->enum('status', [
                'pending',
                'confirmed',
                'completed',
                'canceled'
            ])->default('pending');

            $table->text('reason')->nullable();      // Reason for visit
            $table->text('notes')->nullable();       // Doctor/staff notes
            $table->string('reference_code')->unique(); // e.g. "APT-20240427-001"
            $table->timestamps();

            // Prevent double booking: same doctor, same date, same time
            $table->unique(['doctor_id', 'appointment_date', 'appointment_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};