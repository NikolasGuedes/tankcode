<?php

use App\Http\Controllers\Owner\DirectorController;
use Illuminate\Support\Facades\Route;

Route::get('/directors', [DirectorController::class, 'index'])->name('directors.index');
Route::get('/directors/create', [DirectorController::class, 'create'])->name('directors.create');
Route::get('/directors/{director}/edit', [DirectorController::class, 'edit'])->name('directors.edit');
Route::post('/directors', [DirectorController::class, 'store'])->name('directors.store');
Route::put('/directors/{director}', [DirectorController::class, 'update'])->name('directors.update');
Route::patch('/directors/{director}/access', [DirectorController::class, 'updateAccess'])->name('directors.access.update');
Route::post('/directors/{director}/resend-invitation', [DirectorController::class, 'resendInvitation'])->name('directors.resend-invitation');
Route::delete('/directors/{director}', [DirectorController::class, 'destroy'])->name('directors.destroy');
