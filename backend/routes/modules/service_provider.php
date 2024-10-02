<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RatingsandReviews;
use App\Http\Middleware\RoleAuthentication;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceProviderController;


Route::middleware('auth:sanctum', RoleAuthentication::class . ':service_provider')->group(function () {

    Route::get('/appointments/request', [AppointmentController::class, 'getServiceProviderPendingAppointments']);
    Route::put('/appointments/{id}/update', [AppointmentController::class, 'updateAppointmentStatus']);
    Route::post('/service-provider/toggle-availability', [ServiceProviderController::class, 'toggleAvailability']);
    Route::get('/service-provider/getavailability', [ServiceProviderController::class, 'getAvailability']);
    Route::patch('/service-provider/updateavailability', [ServiceProviderController::class, 'updateAvailability']);
    Route::get('/service-provider/appointments/ongoing', [ServiceProviderController::class, 'getOngoingAppointmentsForServiceProvider']);
    Route::post('/appointments/{id}/complete', [ServiceProviderController::class, 'completeAppointment']);
    Route::get('/my-ratings-reviews', [RatingsandReviews::class, 'getMyRatingsAndReviews']);
    Route::put('/service-provider/update/profile', [AuthController::class, 'updateServiceProviderProfile']);
    Route::get('/profile/serviceprovider', [AuthController::class, 'getServiceProviderProfile']);
    Route::get('/service-provider/analytics', [ServiceProviderController::class, 'getServiceProviderAnalytics']);


});