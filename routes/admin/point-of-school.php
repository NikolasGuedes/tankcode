<?php

use App\Http\Controllers\Admin\PointOfSchoolController;
use Illuminate\Support\Facades\Route;

Route::get('/point-of-schools', [PointOfSchoolController::class, 'index'])->name('point-of-schools.index');
Route::post('/point-of-schools', [PointOfSchoolController::class, 'store'])->name('point-of-schools.store');
Route::put('/point-of-schools/{pointOfSchool}', [PointOfSchoolController::class, 'update'])->name('point-of-schools.update');
Route::delete('/point-of-schools/{pointOfSchool}', [PointOfSchoolController::class, 'destroy'])->name('point-of-schools.destroy');
