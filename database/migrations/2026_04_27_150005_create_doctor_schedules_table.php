<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');

            // 0=Sunday, 1=Monday, ..., 6=Saturday
            $table->tinyInteger('day_of_week');

            $table->time('start_time');
            $table->time('end_time');

            // How many minutes per appointment slot
            $table->integer('slot_duration_minutes')->default(30);

            // Max patients per session
            $table->integer('max_patients')->default(10);

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // A doctor cannot have two schedules on the same day with overlapping times
            $table->unique(['doctor_id', 'day_of_week', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
    }
};