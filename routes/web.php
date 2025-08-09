<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware('isLogin')->group(function () {
    // Login
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'loginProses'])->name('loginProses');
});

// Logout
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::middleware('checkLogin')->group(function () {

    // Dashboard 
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('tugas', [TugasController::class, 'index'])->name('tugas');

    Route::middleware('isAdmin')->group(function () {

        // User
        Route::get('user', [UserController::class, 'index'])->name('user');
        Route::get('user/create', [UserController::class, 'create'])->name('userCreate');
        Route::post('user/store', [UserController::class, 'store'])->name('userStore');
        Route::get('user/edit/{id}', [UserController::class, 'edit'])->name('userEdit');
        Route::post('user/update/{id}', [UserController::class, 'update'])->name('userUpdate');
        Route::delete('user/destroy/{id}', [UserController::class, 'destroy'])->name('userDestroy');

        // Tugas       
        Route::get('tugas/create', [TugasController::class, 'create'])->name('tugasCreate');
        Route::post('tugas/store', [TugasController::class, 'store'])->name('tugasStore');
        Route::get('tugas/edit/{id}', [TugasController::class, 'edit'])->name('tugasEdit');
        Route::post('tugas/update/{id}', [TugasController::class, 'update'])->name('tugasUpdate');
        Route::delete('tugas/destroy/{id}', [TugasController::class, 'destroy'])->name('tugasDestroy');

        // Export
        Route::get('tugas/export/excel', [TugasController::class, 'exportExcel'])->name('tugasExportExcel');
        Route::get('tugas/export/pdf', [TugasController::class, 'exportPdf'])->name('tugasExportPdf');
    });

    Route::post('tugas/upload/{id}', [TugasController::class, 'upload'])->name('tugasUpload');

});

