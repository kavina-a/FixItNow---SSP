


<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RatingsandReviews;
use App\Http\Middleware\RoleAuthentication;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceProviderController;

// Route to get authenticated user information
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// // Register routes for customer and service provider
// require __DIR__ . '/modules/customer.php';
// require __DIR__ . '/modules/service_provider.php';
// // require __DIR__ . '/modules/administrator.php';















// <?php

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AuthController;
// use App\Http\Controllers\PaymentController;
// use App\Http\Controllers\RatingsandReviews;
// use App\Http\Middleware\RoleAuthentication;
// use App\Http\Controllers\AppointmentController;
// use App\Http\Controllers\ServiceProviderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register-customer', [AuthController::class, 'registerCustomer']);
Route::post('/register/service-provider', [AuthController::class, 'registerServiceProvider']);
Route::post('/login', [AuthController::class, 'login']);


// Route::post('/book', [AppointmentController::class, 'bookAppointment']);

// Route::post('/authenticate', [AuthController::class, 'authenticate']);

// require __DIR__ . '/modules/service_provider.php';
// require __DIR__ . '/modules/customer.php';

Route::get('/service-providers/{category}', [ServiceProviderController::class, 'getByCategory']);


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
