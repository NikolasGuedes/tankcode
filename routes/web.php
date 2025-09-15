<?php

use App\Http\Controllers\ClassController;
use App\Http\Controllers\StudentsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::prefix('students')->middleware('auth')->group(function () {
   Route::get('/', [StudentsController::class, 'index'])->name('students');
   Route::post('/store', [StudentsController::class, 'store'])->name('students.store');
   Route::put('/{id}', [StudentsController::class, 'update'])->name('students.update');
   Route::delete('/{id}', [StudentsController::class, 'destroy'])->name('students.destroy');
});

Route::prefix('class')->middleware('auth')->group(function () {
   Route::get('/', [ClassController::class, 'index'])->middleware('auth')->name('class');
});



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
