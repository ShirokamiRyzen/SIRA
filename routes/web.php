<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HeatmapController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OgImageController;
use App\Http\Controllers\ReportController;
use App\Models\Report;
use Illuminate\Support\Facades\Route;

// Halaman Welcome / Landing Page SIRA
Route::get('/', function () {
    $totalReports = Report::count();
    $criticalCount = Report::where('rank_tier', 'critical')->count();
    $urgentCount = Report::where('rank_tier', 'urgent')->count();
    $resolvedCount = Report::where('status', 'resolved')->count();
    $criticalReports = Report::with(['user'])
        ->withCount('comments')
        ->where('rank_tier', '!=', 'normal')
        ->orderByDesc('vote_score')
        ->take(4)
        ->get();

    return view('welcome', compact(
        'totalReports',
        'criticalCount',
        'urgentCount',
        'resolvedCount',
        'criticalReports'
    ));
})->name('home');

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
Route::get('/reports/{report}/og-image', [OgImageController::class, 'report'])->name('reports.ogImage');
Route::get('/og-image/default', [OgImageController::class, 'default'])->name('og.default');

// Fitur Laporan yang Membutuhkan Autentikasi
Route::middleware('auth')->group(function () {
    Route::get('/report/new', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::post('/reports/{report}/vote', [ReportController::class, 'vote'])->name('reports.vote');

    // Update Status Laporan (Hanya untuk pembuat laporan)
    Route::patch('/reports/{report}/status', [ReportController::class, 'updateStatus'])->name('reports.updateStatus');

    // Komentar Bertingkat (Nested Comments)
    Route::post('/reports/{report}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/reports/{report}/comments/{comment}/ai-reply', [CommentController::class, 'generateAiReply'])->name('comments.aiReply');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Notifikasi Pengguna (Mention & Reply)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/stream', [NotificationController::class, 'stream'])->name('notifications.stream');
    Route::match(['get', 'post'], '/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::match(['get', 'post'], '/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::match(['post', 'delete'], '/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clearAll');
});

// Visualisasi Heatmap OpenFreeMap
Route::get('/heatmap', [HeatmapController::class, 'index'])->name('heatmap.index');
Route::get('/api/reports/heatmap', [HeatmapController::class, 'geojson'])->name('api.reports.heatmap');
Route::get('/api/geocode/search', [HeatmapController::class, 'searchLocation'])->name('api.geocode.search');

// Pencarian Mention Pengguna (@) untuk Komentar
Route::get('/api/users/mention', [CommentController::class, 'mentionSuggestions'])->name('api.users.mention');
