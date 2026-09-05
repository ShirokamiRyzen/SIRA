<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HeatmapController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Halaman Welcome
Route::get('/', function () {
    return view('welcome');
});

// Autentikasi Pengguna (Username & Password)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Feed Laporan & Detail Laporan Publik
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');

// Fitur Laporan yang Membutuhkan Autentikasi
Route::middleware('auth')->group(function () {
    Route::get('/report/new', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::post('/reports/{report}/vote', [ReportController::class, 'vote'])->name('reports.vote');

    // Komentar Bertingkat (Nested Comments)
    Route::post('/reports/{report}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// Visualisasi Heatmap OpenFreeMap
Route::get('/heatmap', [HeatmapController::class, 'index'])->name('heatmap.index');
Route::get('/api/reports/heatmap', [HeatmapController::class, 'geojson'])->name('api.reports.heatmap');
