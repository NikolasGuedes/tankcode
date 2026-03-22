<?php

use App\Http\Controllers\Admin\SchoolController;
use Illuminate\Support\Facades\Route;

Route::get('/schools', [SchoolController::class, 'index'])->name('schools.index');
Route::post('/schools', [SchoolController::class, 'store'])->name('schools.store');
Route::put('/schools/{school}', [SchoolController::class, 'update'])->name('schools.update');
Route::delete('/schools/{school}', [SchoolController::class, 'destroy'])->name('schools.destroy');
