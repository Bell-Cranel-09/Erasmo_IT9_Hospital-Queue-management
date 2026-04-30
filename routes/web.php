<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\QueueController;
use Illuminate\Support\Facades\Route;

// ── Landing page ──────────────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) return redirect()->route('dashboard');
    return view('landing');
})->name('landing');

// ── Public patient self-registration ─────────────────────────────────────
Route::get('/register/patient',  [PatientController::class, 'create'])->name('patients.create')->middleware('guest');
Route::post('/register/patient', [PatientController::class, 'store'])->name('patients.store')->middleware('guest');

// ── Authenticated routes ──────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Shortcut: patient views their own profile (show page, not edit)
    Route::get('/my-profile', function () {
        $patient = auth()->user()->patient;
        if (!$patient) return redirect()->route('dashboard')->with('error', 'No patient profile found.');
        return redirect()->route('patients.show', $patient);
    })->name('my-profile');

    // ── Queue ─────────────────────────────────────────────────────────────
    Route::prefix('queue')->name('queue.')->group(function () {
        Route::get('/',                        [QueueController::class, 'index'])->name('index');
        Route::post('/join',                   [QueueController::class, 'join'])->name('join');
        Route::post('/{department}/call-next', [QueueController::class, 'callNext'])->name('call-next');
        Route::get('/{department}/display',    [QueueController::class, 'display'])->name('display');
        Route::delete('/{queue}',              [QueueController::class, 'destroy'])->name('destroy');
    });

    // ── Appointments ──────────────────────────────────────────────────────
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/',                        [AppointmentController::class, 'index'])->name('index');
        Route::get('/create',                  [AppointmentController::class, 'create'])->name('create');
        Route::post('/',                       [AppointmentController::class, 'store'])->name('store');
        Route::get('/slots',                   [AppointmentController::class, 'slots'])->name('slots');
        Route::get('/{appointment}',           [AppointmentController::class, 'show'])->name('show');
        Route::patch('/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('confirm');
        Route::patch('/{appointment}/cancel',  [AppointmentController::class, 'cancel'])->name('cancel');
    });

    // ── Doctors ───────────────────────────────────────────────────────────
    Route::prefix('doctors')->name('doctors.')->group(function () {
        Route::get('/',                                        [DoctorController::class, 'index'])->name('index');
        Route::post('/',                                       [DoctorController::class, 'store'])->name('store');
        Route::get('/{doctor}/schedules',                      [DoctorController::class, 'schedules'])->name('schedules');
        Route::post('/{doctor}/schedules',                     [DoctorController::class, 'storeSchedule'])->name('schedules.store');
        Route::delete('/{doctor}/schedules/{schedule}',        [DoctorController::class, 'destroySchedule'])->name('schedules.destroy');
        Route::patch('/{doctor}/schedules/{schedule}/restore', [DoctorController::class, 'restoreSchedule'])->name('schedules.restore');
    });

    // ── Patients ──────────────────────────────────────────────────────────
    Route::prefix('patients')->name('patients.')->group(function () {
        Route::get('/',               [PatientController::class, 'index'])->name('index');
        Route::get('/{patient}',      [PatientController::class, 'show'])->name('show');
        Route::get('/{patient}/edit', [PatientController::class, 'edit'])->name('edit');
        Route::put('/{patient}',      [PatientController::class, 'update'])->name('update');
        Route::delete('/{patient}',   [PatientController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';
