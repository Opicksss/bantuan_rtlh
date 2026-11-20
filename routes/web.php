<?php

use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\SubKriteriaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenilaianController;

Route::middleware(['guest'])->group(function () {
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login-proses', [AuthController::class, 'login_proses'])->name('login-proses');
    Route::get('forgot', [AuthController::class, 'forgot'])->name('forgot');
    Route::post('forgot-proses', [AuthController::class, 'forgot_proses'])->name('forgot-proses');
    Route::get('verify-code', [AuthController::class, 'verify_code'])->name('verify-code');
    Route::post('verify-code-proses', [AuthController::class, 'verify_code_proses'])->name('verify-code-proses');
    Route::get('reset-password', [AuthController::class, 'reset_password'])->name('reset-password');
    Route::post('reset-password-proses', [AuthController::class, 'reset_password_proses'])->name('reset-password-proses');

});

Route::middleware(['auth'])->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('dashboard', [DashboardController::class, 'dashboard1'])->name('dashboard1');
    Route::get('dashboard2', [DashboardController::class, 'dashboard2'])->name('dashboard2');

    Route::resource('kriteria', KriteriaController::class);

    Route::get('SubKriteria-{id}', [SubKriteriaController::class, 'index'])->name('subKriteria.index');
    Route::post('SubKriteria/', [SubKriteriaController::class, 'store'])->name('subKriteria.store');
    Route::put('SubKriteria-{id}', [SubKriteriaController::class, 'update'])->name('subKriteria.update');
    Route::delete('SubKriteria-{id}', [SubKriteriaController::class, 'destroy'])->name('subKriteria.destroy');

    Route::get('alternatif', [AlternatifController::class, 'index'])->name('alternatif.index');
    Route::post('alternatif/store', [AlternatifController::class, 'store'])->name('alternatif.store');
    Route::put('alternatif/{alternatif}', [AlternatifController::class, 'update'])->name('alternatif.update');
    Route::delete('alternatif/{alternatif}', [AlternatifController::class, 'destroy'])->name('alternatif.destroy');

    Route::get('Unggah_Berkas-{id}', [PenilaianController::class, 'index'])->name('penilaian.index');
    Route::post('Unggah_Berkas/{id}', [PenilaianController::class, 'store'])->name('penilaian.store');
    Route::put('Unggah_Berkas-{id}', [PenilaianController::class, 'update'])->name('penilaian.update');
    Route::delete('Unggah_Berkas-{id}', [PenilaianController::class, 'destroy'])->name('penilaian.destroy');

});
