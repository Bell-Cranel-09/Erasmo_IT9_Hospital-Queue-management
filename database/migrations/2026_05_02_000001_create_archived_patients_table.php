<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archived_patients', function (Blueprint $table) {
            $table->id();

            // Original IDs for reference
            $table->unsignedBigInteger('original_patient_id');
            $table->unsignedBigInteger('original_user_id');

            // Patient data snapshot
            $table->string('patient_code');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->text('medical_history')->nullable();

            // User account data snapshot
            $table->string('email');
            $table->string('user_name');

            // Summary counts (so history isn't lost even after deletion)
            $table->integer('total_appointments')->default(0);
            $table->integer('total_queues')->default(0);

            // Full JSON snapshot of appointments and queues for audit trail
            $table->json('appointments_snapshot')->nullable();
            $table->json('queues_snapshot')->nullable();

            // Who deleted it and when
            $table->unsignedBigInteger('deleted_by_user_id')->nullable();
            $table->string('deleted_by_name')->nullable();
            $table->text('deletion_reason')->nullable();
            $table->timestamp('archived_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archived_patients');
    }
};
