<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\RatingandReview;
use App\Models\ServiceProvider;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RatingsandReviews extends Controller  
{
    /**
     * Add a rating and review for a service provider after an appointment is completed.
     */
    
     public function addRatingAndReview(Request $request)
     {
         try {
             // Check if the user is authenticated
             $user = Auth::user();
             
             if (!$user) {
                return response()->json([
                    'error' => 'Unauthorized: No authenticated user found.',
                    'debug_token' => $request->bearerToken()  // Show the token being passed for debugging
                ], 401);
            }
    
     
             // Find the customer associated with this user
             $customer = Customer::where('user_id', $user->id)->first();
     
             if (!$customer) {
                 return response()->json(['error' => 'No customer profile found for this user.'], 404);
             }
     
             // Validate the request data
             $validated = $request->validate([
                 'serviceprovider_id' => 'required|integer|exists:service_providers,id',
                 'appointment_id' => 'required|integer|exists:appointments,id',
                 'rating' => 'required|integer|min:1|max:5',
                 'review' => 'nullable|string',
             ]);
     
             // Ensure the appointment belongs to the customer and is completed
             $appointment = Appointment::where('id', $validated['appointment_id'])
                 ->where('customer_id', $customer->id)
                 ->where('status', 'completed')
                 ->first();
     
             if (!$appointment) {
                 return response()->json(['error' => 'Invalid appointment or the appointment is not completed yet.'], 400);
             }
     
             // Store the rating and review
             $ratingReview = RatingandReview::create([
                 'customer_id' => $customer->id,
                 'serviceprovider_id' => $validated['serviceprovider_id'],
                 'appointment_id' => $validated['appointment_id'],
                 'rating' => $validated['rating'],
                 'review' => $validated['review'],
             ]);
     
             // Return a success message with the stored review
             return response()->json([
                 'message' => 'Rating and review submitted successfully!',
                 'ratingReview' => $ratingReview,
             ], 201);
     
         } catch (\Illuminate\Database\QueryException $e) {
             // Handle database query errors
             return response()->json([
                 'error' => 'Database error: ' . $e->getMessage()
             ], 500);
         } catch (\Illuminate\Validation\ValidationException $e) {
             // Handle validation errors
             return response()->json([
                 'error' => 'Validation failed.',
                 'details' => $e->errors()
             ], 422);
         } catch (\Exception $e) {
             // Catch any other general errors
             return response()->json([
                 'error' => 'An unexpected error occurred: ' . $e->getMessage()
             ], 500);
         }
     }
     

    /**
     * Get all ratings and reviews for a specific service provider (for public view).
     */

    public function getServiceProviderRatingsAndReviews()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized. No authenticated user found.'], 401);
        }
        
        $customer = Customer::where('user_id', $user->id)->first();
        
        

        $ratingsReviews = RatingandReview::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')  // Sort by newest first
            ->get();

        if ($ratingsReviews->isEmpty()) {
            return response()->json(['message' => 'You have no ratings or reviews yet'], 404);
        }

        return response()->json([
            'ratingReview' => $ratingsReviews
        ]);
    }

    /**
     * Get all ratings and reviews for the logged-in service provider (for the service provider's view).
     */
    public function getMyRatingsAndReviews()
    {
        $user = Auth::user();
        
        $serviceProvider = ServiceProvider::where('user_id', $user->id)->first();
        
        if (!$serviceProvider) {
            return response()->json(['error' => 'Service provider not found'], 404);
        }

        $ratingsReviews = RatingandReview::where('serviceprovider_id', $serviceProvider->id)
            ->orderBy('created_at', 'desc')  // Sort by newest first
            ->get();

        if ($ratingsReviews->isEmpty()) {
            return response()->json(['message' => 'You have no ratings or reviews yet'], 404);
        }

        return response()->json([
            'myRatingsReviews' => $ratingsReviews
        ]);
    }
}
