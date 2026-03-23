<?php

use App\Http\Controllers\Director\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/students', [StudentController::class, 'index'])->name('students.index');
Route::get('/students/template', [StudentController::class, 'downloadTemplate'])->name('students.template');
Route::post('/students/import', [StudentController::class, 'importStudents'])->name('students.import');
Route::post('/students', [StudentController::class, 'store'])->name('students.store');
Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
Route::patch('/students/{student}/access', [StudentController::class, 'updateAccess'])->name('students.access.update');
Route::post('/students/{student}/resend-invitation', [StudentController::class, 'resendInvitation'])->name('students.resend-invitation');
Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
