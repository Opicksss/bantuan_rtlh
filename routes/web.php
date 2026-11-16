<?php

use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\SubKriteriaController;
use Illuminate\Support\Facades\Route;



Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

Route::resource('kriteria', KriteriaController::class);

Route::get('SubKriteri-{id}', [SubKriteriaController::class, 'index'])->name('subKriteria.index');
Route::post('SubKriteria/', [SubKriteriaController::class, 'store'])->name('subKriteria.store');
Route::put('SubKriteria-{id}', [SubKriteriaController::class, 'update'])->name('subKriteria.update');
Route::delete('SubKriteria-{id}', [SubKriteriaController::class, 'destroy'])->name('subKriteria.destroy');

Route::get('alternatif', [AlternatifController::class, 'index'])->name('alternatif.index');
Route::post('alternatif/store', [AlternatifController::class, 'store'])->name('alternatif.store');
Route::put('alternatif/{alternatif}', [AlternatifController::class, 'update'])->name('alternatif.update');
Route::delete('alternatif/{alternatif}', [AlternatifController::class, 'destroy'])->name('alternatif.destroy');
