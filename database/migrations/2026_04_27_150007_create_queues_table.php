<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');

            // Nullable: a queue can be walk-in (no appointment)
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');

            $table->integer('queue_number');         // e.g. 1, 2, 3...
            $table->string('queue_code');            // e.g. "GEN-001", "PED-003"
            $table->date('queue_date');

            $table->enum('status', [
                'waiting',
                'serving',
                'done',
                'skipped'
            ])->default('waiting');

            $table->timestamp('called_at')->nullable();   // When staff called this patient
            $table->timestamp('served_at')->nullable();   // When service started
            $table->timestamp('done_at')->nullable();     // When service ended
            $table->timestamps();

            // Queue numbers reset per department per day
            $table->unique(['department_id', 'queue_date', 'queue_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};