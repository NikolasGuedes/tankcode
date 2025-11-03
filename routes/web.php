<?php

use App\Http\Controllers\StudentsController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\StudentEmailVerificationController;
use App\Http\Controllers\StudentPasswordResetController;
use App\Http\Controllers\RoomsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Rotas para área administrativa dos estudantes (admin)
Route::prefix('students')->middleware('auth')->group(function () {
   Route::get('/', [StudentsController::class, 'index'])->name('students');
   Route::post('/store', [StudentsController::class, 'store'])->name('students.store');
   Route::post('/import', [StudentsController::class, 'import'])->name('students.import');
   Route::get('/download-template', [StudentsController::class, 'downloadTemplate'])->name('students.download-template');
   Route::put('/{id}', [StudentsController::class, 'update'])->name('students.update');
   Route::delete('/{id}', [StudentsController::class, 'destroy'])->name('students.destroy');
   Route::post('/{id}/toggle-access', [StudentsController::class, 'togglePlatformAccess'])->name('students.toggle-access');
   Route::post('/{id}/resend-verification', [StudentsController::class, 'resendVerificationEmail'])->name('students.resend-verification');
});

// Rotas para área do estudante (login próprio)
Route::prefix('student')->group(function () {
    Route::get('/login', [StudentAuthController::class, 'showLogin'])->name('student.login');
    Route::post('/login', [StudentAuthController::class, 'login'])->name('student.login.submit');
    Route::get('/logout', [StudentAuthController::class, 'logout'])->name('student.logout');
    
    // Rotas de reset de senha
    Route::get('/forgot-password', [StudentPasswordResetController::class, 'showRequestForm'])->name('student.password.request');
    Route::post('/forgot-password', [StudentPasswordResetController::class, 'sendResetLinkEmail'])->name('student.password.email');
    Route::get('/reset-password/{token}', [StudentPasswordResetController::class, 'showResetForm'])->name('student.password.reset');
    Route::post('/reset-password', [StudentPasswordResetController::class, 'reset'])->name('student.password.update');
    
    Route::middleware('student.auth')->group(function () {
        Route::get('/dashboard', [StudentAuthController::class, 'dashboard'])->name('student.dashboard');
    });
});
// Route::resource('rooms', RoomsController::class)
//     ->only(['index', 'store', 'update'])
//     ->middleware('auth')
//     ->names('rooms');

Route::prefix('rooms')->middleware('auth')->group(function () {
    Route::get('/', [RoomsController::class, 'index'])->name('rooms.index');
    Route::post('/store', [RoomsController::class, 'store'])->name('rooms.store');
    Route::put('/{id}', [RoomsController::class, 'update'])->name('rooms.update');
    Route::get('/{id}/edit', [RoomsController::class, 'editrooms'])->name('rooms.editrooms');
    Route::get('/{id}/edit', [RoomsController::class, 'editrooms'])->name('rooms.EditRooms');
    Route::post('/{room}/add-student', [RoomsController::class, 'addStudent'])->name('rooms.addStudent');
    Route::delete('/{room}/remove-student/{student}', [RoomsController::class, 'removeStudent'])->name('rooms.removeStudent');
});


// Rotas de verificação de email e criação de senha do estudante
Route::get('/student/verify-email/{token}', [StudentEmailVerificationController::class, 'verify'])
    ->name('student.verify-email');

Route::post('/student/create-password', [StudentEmailVerificationController::class, 'createPassword'])
    ->name('student.create-password');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
