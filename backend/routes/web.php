<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\RoleAuthentication;
use App\Http\Controllers\AdminDashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });


// User Analytics
Route::get('/admin/dashboard/users', [AdminDashboardController::class, 'getTotalUsers'])->name('admin.analytics.users');
Route::get('/admin/dashboard/bookings-heatmap', [AdminDashboardController::class, 'getBookingHeatmap'])->name('admin.analytics.heatmap');
Route::get('/admin/dashboard/growth-rate', [AdminDashboardController::class, 'getGrowthRate'])->name('admin.analytics.growthRate');

// Service Provider Analytics
Route::get('/admin/dashboard/top-categories', [AdminDashboardController::class, 'getTopServiceCategories'])->name('admin.analytics.topCategories');

// wont work cause of the reviews and ratings not working as for now
Route::get('/admin/dashboard/top-providers', [AdminDashboardController::class, 'getTopServiceProviders'])->name('admin.analytics.topProviders');

// Booking Analytics
Route::get('/admin/dashboard/total-bookings', [AdminDashboardController::class, 'getTotalBookings'])->name('admin.analytics.totalBookings');
Route::get('/admin/dashboard/peak-hours', [AdminDashboardController::class, 'getPeakBookingHours'])->name('admin.analytics.peakHours');
Route::get('/admin/dashboard/avg-completion-time', [AdminDashboardController::class, 'getAvgCompletionTime'])->name('admin.analytics.avgCompletionTime');
Route::get('/admin/dashboard/avg-response-time', [AdminDashboardController::class, 'getAvgResponseTime'])->name('admin.analytics.avgResponseTime');

// Revenue Analytics
Route::get('/admin/dashboard/total-revenue', [AdminDashboardController::class, 'getTotalRevenue'])->name('admin.analytics.totalRevenue');
Route::get('/admin/dashboard/revenue-by-category', [AdminDashboardController::class, 'getRevenueByCategory'])->name('admin.analytics.revenueByCategory');

// Forecast Analytics
Route::get('/admin/dashboard/booking-forecast', [AdminDashboardController::class, 'getBookingForecast'])->name('admin.analytics.bookingForecast');

Route::get('/admin/reviews/moderation', [AdminDashboardController::class, 'index'])->name('reviews.moderation');
Route::post('/admin/review/{id}/approve', [AdminDashboardController::class, 'approve'])->name('review.approve');
Route::post('/admin/review/{id}/reject', [AdminDashboardController::class, 'reject'])->name('review.reject');

Route::get('/service-providers', [AdminDashboardController::class, 'showWorkers'])->name('admin.spview');


Route::get('/admin/login', function () {
    return view('auth.admin-login'); // Your Jetstream login page view
})->name('admin.login');


Route::post('/admin/login', [AuthController::class, 'loginAdmin'])->name('admin.login.submit');


Route::middleware(['auth:sanctum', RoleAuthentication::class . ':administrator'])->group(function () {

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');

});


// // Route::middleware('auth:sanctum', RoleAuthentication::class . ':customer'])->group(function () {
