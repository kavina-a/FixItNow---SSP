<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RatingsandReviews;
use App\Http\Middleware\RoleAuthentication;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceProviderController;

Route::middleware(['auth:sanctum', RoleAuthentication::class . ':customer'])->group(function () {
    
    // Route for booking an appointment
    Route::post('/book', [AppointmentController::class, 'bookAppointment']);

    // Route for getting pending appointments
    Route::get('/appointments/pending', [AppointmentController::class, 'getPendingAppointments']);

    Route::get('/service-provider/{id}', [ServiceProviderController::class, 'getServiceProviderDetails']);

    // Route for getting rejected appointments
    Route::get('/appointments/rejected', [AppointmentController::class, 'getRejectedAppointments']);

    // Route for marking the appointment as seen
    Route::post('/appointments/mark-seen', [AppointmentController::class, 'markAppointmentAsSeen']);

    // Get Completed Appointments for a customer
    Route::get('/appointments/completed', [AppointmentController::class, 'getCompletedAppointments']);

    // Get Ongoing Appointments for a customer
    Route::get('/appointments/ongoing', [AppointmentController::class, 'getOngoingAppointments']);

    // Route to add a rating and review for a service provider
    Route::post('/rating-review', [RatingsandReviews::class, 'addRatingAndReview']);

    // Route to get all ratings and reviews for a specific service provider (public view)
    Route::get('/service-provider/{id}/ratings-reviews', [RatingsandReviews::class, 'getServiceProviderRatingsAndReviews']);

    Route::put('/customer/profile', [AuthController::class, 'updateCustomerProfile']);
    Route::get('/profile', [AuthController::class, 'getCustomerProfile']);

    Route::post('/payment/charge', [PaymentController::class, 'charge']);
    Route::post('/payment/intent', [PaymentController::class, 'createPaymentIntent']);

});