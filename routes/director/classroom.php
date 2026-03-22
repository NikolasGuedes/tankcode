<?php

use App\Http\Controllers\Director\ClassroomController;
use Illuminate\Support\Facades\Route;

Route::get('/classrooms', [ClassroomController::class, 'index'])->name('classrooms.index');
Route::get('/classrooms/create', [ClassroomController::class, 'create'])->name('classrooms.create');
Route::get('/classrooms/{classroom}/edit', [ClassroomController::class, 'edit'])->name('classrooms.edit');
Route::post('/classrooms', [ClassroomController::class, 'store'])->name('classrooms.store');
Route::put('/classrooms/{classroom}', [ClassroomController::class, 'update'])->name('classrooms.update');
Route::delete('/classrooms/{classroom}', [ClassroomController::class, 'destroy'])->name('classrooms.destroy');
