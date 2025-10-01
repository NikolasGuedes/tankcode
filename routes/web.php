<?php

use App\Http\Controllers\StudentsController;
use App\Http\Controllers\StudentAuthController;
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
   Route::put('/{id}', [StudentsController::class, 'update'])->name('students.update');
   Route::delete('/{id}', [StudentsController::class, 'destroy'])->name('students.destroy');
});

// Rotas para área do estudante (login próprio)
Route::prefix('student')->group(function () {
    Route::get('/login', [StudentAuthController::class, 'showLogin'])->name('student.login');
    Route::post('/login', [StudentAuthController::class, 'login'])->name('student.login.submit');
    Route::get('/logout', [StudentAuthController::class, 'logout'])->name('student.logout');
    
    // Rota para troca obrigatória de senha (precisa estar logado mas não precisa ter senha alterada)
    Route::middleware('student.auth')->group(function () {
        Route::get('/change-password', [StudentAuthController::class, 'showChangePassword'])->name('student.change-password');
        Route::post('/change-password', [StudentAuthController::class, 'changePassword'])->name('student.change-password.submit');
    });
    
    Route::middleware('student.auth')->group(function () {
        Route::get('/dashboard', [StudentAuthController::class, 'dashboard'])->name('student.dashboard');
    });
});

Route::prefix('rooms')->middleware('auth')->group(function () {
    Route::get('/', [RoomsController::class, 'index']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
