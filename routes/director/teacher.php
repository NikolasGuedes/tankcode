<?php

use App\Http\Controllers\Director\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
Route::patch('/teachers/{teacher}/access', [TeacherController::class, 'updateAccess'])->name('teachers.access.update');
Route::post('/teachers/{teacher}/resend-invitation', [TeacherController::class, 'resendInvitation'])->name('teachers.resend-invitation');
Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
