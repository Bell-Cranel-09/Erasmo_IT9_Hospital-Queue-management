<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\QueueController;
use Illuminate\Support\Facades\Route;

// ── Landing page (public) ─────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) return redirect()->route('dashboard');
    return view('landing');
})->name('landing');

// ── Authenticated routes ──────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Queue ─────────────────────────────────────────────────────────────
    Route::prefix('queue')->name('queue.')->group(function () {
        Route::get('/',                         [QueueController::class, 'index'])->name('index');
        Route::post('/join',                    [QueueController::class, 'join'])->name('join');
        Route::post('/{department}/call-next',  [QueueController::class, 'callNext'])->name('call-next');
        Route::get('/{department}/display',     [QueueController::class, 'display'])->name('display');
        Route::delete('/{queue}',               [QueueController::class, 'destroy'])->name('destroy'); // ← NEW
    });

    // ── Appointments ──────────────────────────────────────────────────────
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/',                         [AppointmentController::class, 'index'])->name('index');
        Route::get('/create',                   [AppointmentController::class, 'create'])->name('create');
        Route::post('/',                        [AppointmentController::class, 'store'])->name('store');
        Route::get('/slots',                    [AppointmentController::class, 'slots'])->name('slots');
        Route::get('/{appointment}',            [AppointmentController::class, 'show'])->name('show');
        Route::patch('/{appointment}/confirm',  [AppointmentController::class, 'confirm'])->name('confirm');
        Route::patch('/{appointment}/cancel',   [AppointmentController::class, 'cancel'])->name('cancel');
    });

    // ── Doctors ───────────────────────────────────────────────────────────
    Route::prefix('doctors')->name('doctors.')->group(function () {
        Route::get('/',                         [DoctorController::class, 'index'])->name('index');
        Route::post('/',                        [DoctorController::class, 'store'])->name('store');
        Route::get('/{doctor}/schedules',       [DoctorController::class, 'schedules'])->name('schedules');
        Route::post('/{doctor}/schedules',      [DoctorController::class, 'storeSchedule'])->name('schedules.store');
    });

    // ── Patients ──────────────────────────────────────────────────────────
    Route::prefix('patients')->name('patients.')->group(function () {
        Route::get('/',                         [PatientController::class, 'index'])->name('index');
        Route::get('/create',                   [PatientController::class, 'create'])->name('create');
        Route::post('/',                        [PatientController::class, 'store'])->name('store');
        Route::get('/{patient}',                [PatientController::class, 'show'])->name('show');
        Route::get('/{patient}/edit',           [PatientController::class, 'edit'])->name('edit');
        Route::put('/{patient}',                [PatientController::class, 'update'])->name('update');
        Route::delete('/{patient}',             [PatientController::class, 'destroy'])->name('destroy'); // ← NEW
    });
});

// ── Auth routes (Laravel Breeze) ──────────────────────────────────────────
require __DIR__.'/auth.php';
