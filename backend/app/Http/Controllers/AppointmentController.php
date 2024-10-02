<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\RatingandReview;
use App\Models\ServiceProvider;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{

    //* FUNCTION FOR BOOKING AN APPOINTMENT FROM THE CUSTOMERS POV
    public function bookAppointment(Request $request) {

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized. No authenticated user found.'], 401);
        }

        $customer = Customer::where('user_id', $user->id)->first();

        // Validate the request input
        $request->validate([
            'serviceprovider_id' => 'required|integer',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'notes' => 'nullable|string',
            'price' => 'required|integer',
            'service_type' => 'required|string',
        ]);

        // Reverse geocode latitude and longitude into a human-readable address
        $location = $this->getAddressFromLatLng($request->latitude, $request->longitude);

        $appointment = Appointment::create([
            'customer_id' => $customer->id,  
            'serviceprovider_id' => $request->serviceprovider_id,
            'start_time' => $request->start_time,
            'location' => $location,  
            'notes' => $request->notes,
            'price' => $request->price,  
            'service_type' => $request->service_type,
            'status' => 'pending',  
            'payment_status' => 'pending',  
        ]);

        return response()->json([
            'message' => 'Appointment successfully booked!',
            'appointment' => $appointment
        ], 201);
    }


    //* Function to get address from latitude and longitude using Google Geocoding API
    private function getAddressFromLatLng($latitude, $longitude) {
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}";

        $response = file_get_contents($url);
        $json = json_decode($response, true);

        if ($json['status'] == 'OK') {
            // Extract formatted address from the JSON response
            return $json['results'][0]['formatted_address'];
        } else {
            return 'Unknown location';
        }
    }


    //* FUNCTION TO SEE ALL pending set of appointments for the customer
    public function getPendingAppointments(Request $request) {

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized. No authenticated user found.'], 401);
        }

        $customer = Customer::where('user_id', $user->id)->first();


        $pendingAppointments = Appointment::where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->with('serviceProvider:id,first_name')  
            ->orderBy('start_time', 'asc')  
            ->get()
            ->map(function ($appointment) {
                $appointment->service_provider_name = $appointment->serviceProvider->first_name;
                unset($appointment->serviceProvider);
                return $appointment;
            });

        return response()->json([
            'message' => 'Pending appointments retrieved successfully!',
            'appointments' => $pendingAppointments
        ], 200);
    }


    //* FUNCTION TO SEE ALL pending set of appointments for the service provider
   public function getServiceProviderPendingAppointments(Request $request) {
        $user = Auth::user();


        if (!$user) {
            return response()->json(['error' => 'Unauthorized. No authenticated service provider found.'], 401);
        }

        $serviceProvider = ServiceProvider::where('user_id', $user->id)->first();

        if (!$serviceProvider) {
            return response()->json(['error' => 'No associated service provider found for this user.'], 404);
        }

        $pendingAppointments = Appointment::where('serviceprovider_id', $serviceProvider->id) // Use service provider ID
            ->where('status', 'pending')
            ->with('customer:id,first_name')  // Eager-load the customer's first name
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($appointment) {
                // Add customer's name to the response
                $appointment->customer_name = $appointment->customer->first_name;
                unset($appointment->customer); // Optionally remove the nested customer data
                return $appointment;
            });

        return response()->json([
            'message' => 'Pending appointments retrieved successfully!',
            'appointments' => $pendingAppointments
        ], 200);
    }


    //* FUNCTION TO ACCEPT AND DECLINE THE APPOINTMENT  -  SERVICE PROVIDERS POV
    public function updateAppointmentStatus(Request $request, $appointmentId) {
        // Get the authenticated service provider
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['error' => 'Unauthorized. No authenticated service provider found.'], 401);
        }
    
        // Find the appointment
        $appointment = Appointment::find($appointmentId);
    
        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found.'], 404);
        }
    
        $serviceProvider = ServiceProvider::where('user_id', $user->id)->first();

        if ($appointment->serviceprovider_id !== $serviceProvider->id) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }
    
        $request->validate([
            'status' => 'required|in:accepted,declined',
            'declined_reason' => 'required_if:status,declined|string|nullable',
        ]);
    
        // Update appointment status
        $appointment->status = $request->status;
        if ($request->status === 'declined') {
            $appointment->declined_reason = $request->declined_reason;
            $appointment->rejection_seen = false;

        }
        $appointment->save();
    
        return response()->json(['message' => 'Appointment updated successfully!'], 200);
    }

    //* FUNCTION TO GET THE REJECTED APPOINTMENT MESSAGE FOR THE CUSTOMER 
    public function getRejectedAppointments(Request $request) {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['error' => 'Unauthorized. No authenticated user found.'], 401);
        }
    
        $customer = Customer::where('user_id', $user->id)->first();
    
        // Fetch rejected appointments
        $rejectedAppointments = Appointment::where('customer_id', $customer->id)
        ->where('status', 'declined')
        ->where('rejection_seen', false)
        ->with('serviceProvider:id,first_name')  
        ->get()
        ->map(function ($appointment) {
            // Add service provider's name to appointment
            $appointment->service_provider_name = $appointment->serviceProvider->first_name;
            unset($appointment->serviceProvider); // Optional cleanup
            return $appointment;
        });
        return response()->json([
            'rejectedAppointments' => $rejectedAppointments
        ], 200);
    }

    //* FUNCTION TO CHECK IF A CUSTOMER HAS SEEN THE REJECTION MESSAGE - WILL BE USED FOR ANALYTICS
    public function markAppointmentAsSeen(Request $request) {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['error' => 'Unauthorized. No authenticated user found.'], 401);
        }
    
        $customer = Customer::where('user_id', $user->id)->first();
    
        // Check if the user is marking any appointments as seen
        if ($request->has('appointment_id')) {
            $appointment = Appointment::where('id', $request->appointment_id)
                                      ->where('customer_id', $customer->id)
                                      ->first();
            
            if ($appointment) {
                $appointment->rejection_seen = true;
                $appointment->save();
                return response()->json(['message' => 'Appointment marked as seen'], 200);
            }
        }
    
        return response()->json(['error' => 'Appointment not found or unauthorized access'], 404);
    }


    public function getOngoingAppointments(Request $request) {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['error' => 'Unauthorized. No authenticated user found.'], 401);
        }
    
        $customer = Customer::where('user_id', $user->id)->first();
    
        // Fetch ongoing appointments
        $ongoingAppointments = Appointment::where('customer_id', $customer->id)
            ->where('status', 'accepted')
            ->with('serviceProvider:id,first_name')  // Fetch related service provider
            ->get()
            ->map(function ($appointment) {
                $appointment->service_provider_name = $appointment->serviceProvider->first_name;
                unset($appointment->serviceProvider); // Optional cleanup
                return $appointment;
            });
    
        return response()->json([
            'ongoingAppointments' => $ongoingAppointments
        ], 200);
    }
    

    //* FUNCTION TO GET ALL PAST BOOKINGS FOR CUSTOMER 
    public function getCompletedAppointments(Request $request) {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['error' => 'Unauthorized. No authenticated user found.'], 401);
        }
    
        $customer = Customer::where('user_id', $user->id)->first();
    
        $completedAppointments = Appointment::where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->with('serviceProvider:id,first_name')  // Fetch related service provider
            ->get()
            ->map(function ($appointment) {
                // Add service provider's name to appointment
                $appointment->service_provider_name = $appointment->serviceProvider->first_name;
    
                // Return relative proof image path
                $appointment->proof_image = $appointment->proof_image ? $appointment->proof_image : null;
    
                $hasReview = RatingandReview::where('appointment_id', $appointment->id)->exists();
                $appointment->has_review = $hasReview;  // Add has_review field
    
                unset($appointment->serviceProvider); // Optional cleanup
                return $appointment;
            });
    
        return response()->json([
            'completedAppointments' => $completedAppointments
        ], 200);
    }
    
    
}