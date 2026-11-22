<?php

use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HasilSawController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\ManagementAkunController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PerangkinganController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubKriteriaController;
use App\Http\Controllers\VerifikatorDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
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
    Route::middleware(['role:admin'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

        Route::resource('kriteria', KriteriaController::class);

        Route::get('SubKriteria-{id}', [SubKriteriaController::class, 'index'])->name('subKriteria.index');
        Route::post('SubKriteria/', [SubKriteriaController::class, 'store'])->name('subKriteria.store');
        Route::put('SubKriteria-{id}', [SubKriteriaController::class, 'update'])->name('subKriteria.update');
        Route::delete('SubKriteria-{id}', [SubKriteriaController::class, 'destroy'])->name('subKriteria.destroy');

        Route::get('/hasil-saw', [HasilSawController::class, 'index'])->name('hasil-saw.index');
        Route::post('/hasil-saw', [HasilSawController::class, 'simpan'])->name('hasil-saw.simpan');
        Route::delete('/hasil-saw', [HasilSawController::class, 'destroy'])->name('hasil-saw.destroy');

        Route::get('/perangkingan', [PerangkinganController::class, 'index'])->name('perangkingan.index');

        Route::get('management-akun-admin', [ManagementAkunController::class, 'admin'])->name('management-akun.admin');
        Route::get('management-akun-verifikator', [ManagementAkunController::class, 'verifikator'])->name('management-akun.verifikator');
        Route::post('management-akun-store', [ManagementAkunController::class, 'store'])->name('management-akun.store');
        Route::put('management-akun-update/{id}', [ManagementAkunController::class, 'update'])->name('management-akun.update');
        Route::delete('management-akun-destroy/{id}', [ManagementAkunController::class, 'destroy'])->name('management-akun.destroy');
    });
    Route::middleware(['role:verifikator'])->group(function () {
        Route::get('verifikator-dashboard', [VerifikatorDashboardController::class, 'dashboard'])->name('verifikator-dashboard');
    });

    Route::middleware(['role:admin,verifikator'])->group(function () {
        Route::get('alternatif', [AlternatifController::class, 'index'])->name('alternatif.index');
        Route::post('alternatif/store', [AlternatifController::class, 'store'])->name('alternatif.store');
        Route::put('alternatif/{alternatif}', [AlternatifController::class, 'update'])->name('alternatif.update');
        Route::delete('alternatif/{alternatif}', [AlternatifController::class, 'destroy'])->name('alternatif.destroy');

        Route::get('Unggah_Berkas-{id}', [PenilaianController::class, 'index'])->name('penilaian.index');
        Route::post('Unggah_Berkas/{id}', [PenilaianController::class, 'store'])->name('penilaian.store');
        Route::delete('Unggah_Berkas-{id}', [PenilaianController::class, 'destroy'])->name('penilaian.destroy');
    });

    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('profile-{id}', [ProfileController::class, 'profile'])->name('profile');
    Route::post('profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});
