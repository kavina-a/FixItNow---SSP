<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\RatingandReview;
use Illuminate\Support\Facades\Auth;

class ServiceProviderController extends Controller
{
    /**
     * Get service providers by category.
     *
     * @param string $category
     * @return \Illuminate\Http\JsonResponse
     */

     public function toggleAvailability(Request $request) {
        $user = Auth::user();

        // Find the service provider record
        $serviceProvider = ServiceProvider::where('user_id', $user->id)->first();
        
        if (!$serviceProvider) {
            return response()->json(['error' => 'Service provider not found'], 404);
        }

        // Update the availability status
        $serviceProvider->availability = $request->availability;
        $serviceProvider->save();

        return response()->json(['message' => 'Availability updated successfully']);
}

     
public function getByCategory($category, Request $request)
{
    $customerLatitude = $request->query('latitude');
    $customerLongitude = $request->query('longitude');

    // Fetch service providers for the category
    $providers = ServiceProvider::whereJsonContains('service_type', $category)->get();

    // Calculate distance and time for each service provider
    $providers = $providers->map(function ($provider) use ($customerLatitude, $customerLongitude) {
        $serviceArea = $provider->city;
        $serviceAreaCoordinates = $this->getCoordinatesFromAddress($serviceArea);
        $distanceAndTime = $this->calculateDistanceUsingGoogleAPI(
            $serviceAreaCoordinates['lat'],
            $serviceAreaCoordinates['lng'],
            $customerLatitude,
            $customerLongitude
        );

        // Add distance and estimated time to the provider data
        $provider->distance = $distanceAndTime['distance'];
        $provider->estimated_time = $distanceAndTime['duration'];

        // Add full profile image URL
        $provider->profile_image = $provider->profile_image 
            ? url('storage/' . $provider->profile_image) 
            : null;

        // Decode service_type and languages fields
        $provider->service_type = json_decode($provider->service_type, true);  // Decode JSON to array
        $provider->languages = json_decode($provider->languages, true);  // Decode JSON to array

        return $provider;
    });

    // Sort the providers by estimated time in ascending order
    $sortedProviders = $providers->sortBy('estimated_time');

    return response()->json([
        'message' => 'Service providers found',
        'data' => $sortedProviders->values()->all()  // Reindex after sorting
    ], 200);
}





public function getServiceProviderDetails($id, Request $request)
{
    // Get the service provider details
    $serviceProvider = ServiceProvider::find($id);

    // Get the customer's latitude and longitude from the request (from the frontend)
    $customerLatitude = $request->query('latitude');
    $customerLongitude = $request->query('longitude');

    // Get the service provider's service area (city)
    $serviceArea = $serviceProvider->city;

    // Convert service area to coordinates using Google Geocoding API
    $serviceAreaCoordinates = $this->getCoordinatesFromAddress($serviceArea);

    // Calculate the distance and time between the service area and customer's location
    $distanceAndTime = $this->calculateDistanceUsingGoogleAPI(
        $serviceAreaCoordinates['lat'], 
        $serviceAreaCoordinates['lng'], 
        $customerLatitude, 
        $customerLongitude
    );

    // Return the service provider details along with the calculated distance and time
    return response()->json([
        'id' => $serviceProvider->id,
        'first_name' => $serviceProvider->first_name,
        'last_name' => $serviceProvider->last_name,
        'phone_number' => $serviceProvider->phone_number,
        'address' => $serviceProvider->address,
        'city' => $serviceProvider->city,
        'service_type' => json_decode($serviceProvider->service_type), // Decode to array
        'years_of_experience' => $serviceProvider->years_of_experience,
        'availability' => $serviceProvider->availability,
        'description' => $serviceProvider->description,
        'languages' => json_decode($serviceProvider->languages), // Decode to array
        'profile_image' => $serviceProvider->profile_image ? asset('storage/' . $serviceProvider->profile_image) : null,  // Generate full URL for image
        'distance' => $distanceAndTime['distance'],  // Include the calculated distance
        'estimated_time' => $distanceAndTime['duration'],  // Include the calculated estimated time
    ]);
}

public function getCoordinatesFromAddress($address)
{
    $apiKey = env('GOOGLE_MAPS_API_KEY');
    $address = urlencode($address);

    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}";
    
    // Log the API request URL
    Log::info("Geocode API request: $url");
    
    $response = file_get_contents($url);
    $json = json_decode($response, true);

    // Log the response from Google API
    Log::info("Geocode API response: " . json_encode($json));

    if ($json['status'] == 'OK') {
        $latitude = $json['results'][0]['geometry']['location']['lat'];
        $longitude = $json['results'][0]['geometry']['location']['lng'];

        return ['lat' => $latitude, 'lng' => $longitude];
    } else {
        // Log an error if the geocoding fails
        Log::error("Failed to get coordinates for address: $address");
        throw new Exception('Failed to get coordinates for address.');
    }
}


public function calculateDistanceUsingGoogleAPI($serviceLat, $serviceLng, $customerLat, $customerLng)
{
    $apiKey = env('GOOGLE_MAPS_API_KEY');
    
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$serviceLat},{$serviceLng}&destinations={$customerLat},{$customerLng}&key={$apiKey}";

    $response = file_get_contents($url);
    $json = json_decode($response, true);

    if ($json['status'] == 'OK') {
        $distance = $json['rows'][0]['elements'][0]['distance']['text'];
        $duration = $json['rows'][0]['elements'][0]['duration']['text'];

        return ['distance' => $distance, 'duration' => $duration];
    } else {
        throw new Exception('Failed to calculate distance.');
    } 
}


    public function getAvailability(Request $request)
    {
        $user = Auth::user();

        $serviceProvider = ServiceProvider::where('user_id', $user->id)->first();
        
        if (!$serviceProvider) {
            return response()->json(['error' => 'Service provider not found'], 404);
        }

        return response()->json([
            'availability' => $serviceProvider->availability,
        ]);
    }

    public function updateAvailability(Request $request)
    {
        $request->validate([
            'availability' => 'required|boolean',
        ]);
        $user = Auth::user();

        $serviceProvider = ServiceProvider::where('user_id', $user->id)->first();

        $serviceProvider->availability = $request->availability;
        $serviceProvider->save();

        return response()->json(['message' => 'Availability updated successfully']);

    }

    public function getOngoingAppointmentsForServiceProvider(Request $request) {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['error' => 'Unauthorized. No authenticated user found.'], 401);
        }
    
        $serviceProvider = ServiceProvider::where('user_id', $user->id)->first();
    
        if (!$serviceProvider) {
            return response()->json(['error' => 'No service provider found for this user.'], 404);
        }
    
        // Fetch ongoing appointments for the service provider
        $ongoingAppointments = Appointment::where('serviceprovider_id', $serviceProvider->id)
            ->where('status', 'accepted')
            ->with('customer:id,first_name')  // Fetch related customer
            ->get()
            ->map(function ($appointment) {
                // Add customer's name to appointment
                $appointment->customer_name = $appointment->customer->first_name;
                unset($appointment->customer); // Optional cleanup
                return $appointment;
            });
    
        return response()->json([
            'ongoingAppointments' => $ongoingAppointments
        ], 200);
    }

    public function completeAppointment(Request $request, $id)
{
    // Validate the request
    $request->validate([
        'proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',  // Image is optional
    ]);

    // Find the appointment
    $appointment = Appointment::findOrFail($id);

    // Ensure the appointment is still ongoing (status is 'accepted')
    if ($appointment->status !== 'accepted') {
        return response()->json(['error' => 'Appointment is not ongoing or has already been completed.'], 400);
    }

    // If there's an image provided, store it
    if ($request->hasFile('proof')) {
        $image = $request->file('proof');
        $path = $image->store('proofs', 'public');  // Store the image in 'storage/app/public/proofs' directory
        $appointment->proof_image = $path;  // Save the image path in the database
    }

    // Mark the appointment as completed
    $appointment->status = 'completed';
    $appointment->completed_at = now();  // Add completion timestamp
    $appointment->save();

    return response()->json(['message' => 'Appointment marked as completed']);
}

    public function getServiceProviderAnalytics(Request $request) {
        $user = Auth::user(); // Assuming the service provider is authenticated

        $serviceProvider = ServiceProvider::where('user_id', $user->id)->first();

        // Fetch the number of completed appointments
        $completedAppointments = Appointment::where('service_provider_id', $serviceProvider->id)
                                            ->where('status', 'completed')
                                            ->count();

        // Fetch the service provider's average rating and total number of reviews
        $averageRating = RatingandReview::where('service_provider_id', $user->id)
                            ->avg('rating');
        
        $totalReviews = RatingandReview::where('service_provider_id', $user->id)
                            ->count();

        // Prepare the analytics data
        $analyticsData = [
            'completed_appointments' => $completedAppointments,
            'average_rating' => $averageRating,
            'total_reviews' => $totalReviews
        ];

        // Return the data as JSON
        return response()->json($analyticsData);
    }

}