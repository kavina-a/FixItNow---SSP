<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\UserRole;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\ServiceProvider;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CustomerResource;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\ServiceProviderResource;
use Illuminate\Support\Facades\Log;  // Make sure this is imported


class AuthController extends Controller
{
    //? wb using a flag and then sending the request , then u can do it is_register = true or false
    //? what are the times u should pass the token - eg when viewing all the ppl in a particular category and booking an appt
 
    public function loginAdmin(Request $request)
{
    // Validate the input
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    // Find the user by email
    $user = User::where('email', $request->input('email'))->first();

    if ($user) {
        // Check if the password is correct
        if (Hash::check($request->input('password'), $user->password)) {
            // Debug: Output the user's role and expected role
            // dd('User Role:', $user->role->value, 'Expected Role:', UserRole::Administrator->value);
            
            // Check if the user's role matches the expected administrator role
            if ($user->role->value === UserRole::Administrator->value) { // Access value property
                Auth::login($user);
                return redirect()->route('admin.dashboard');
            } else {
                // If the user is not an admin, deny the login
                return back()->withErrors([
                    'error' => 'Only administrators can log in.',
                ]);
            }
        } else {
            // Invalid password
            return back()->withErrors([
                'error' => 'Invalid password.',
            ]);
        }
    } else {
        // User not found
        return back()->withErrors([
            'error' => 'No user found with this email.',
        ]);
    }
}

    // what else are the must and must details 
    public function login(Request $request)
    {
        try {
            // Validate the request
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:8',
            ]);
    
            // Attempt to find the user by email
            $user = User::where('email', $credentials['email'])->first();
    
            // Check if the user exists and if the password is correct
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
    
            // Generate the token
            $token = $user->createToken('auth_token')->plainTextToken;
    
            // Check if the user is a customer
            if ($user->role->value === UserRole::Customer->value) {
                $profile = Customer::where('user_id', $user->id)->first();
    
                return response()->json([
                    'message' => 'Login successful as Customer',
                    'token' => $token,
                    'user' => new UserResource($user),
                    'profile' => new CustomerResource($profile), 
                ], 200);
    
            } elseif ($user->role->value === UserRole::ServiceProvider->value) {
                $profile = ServiceProvider::where('user_id', $user->id)->first();
    
                return response()->json([
                    'message' => 'Login successful as Service Provider',
                    'token' => $token,
                    'user' => new UserResource($user),
                    'profile' => new ServiceProviderResource($profile), 
                ], 200);
    
            } else {
                return response()->json([
                    'message' => 'Role not recognized',
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    //* register customer
    public function registerCustomer(Request $request)
{
    // Validate the request
    $validated = $request->validate([
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'phone_number' => 'required|string',
        'address' => 'required|string',
        'city' => 'required|string',
    ]);

    // Create user with role from enum
    $user = User::create([
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => UserRole::Customer->value,  // Use enum for role
    ]);

    // Create customer profile, ensuring the user_id is set
    $profile = $user->customer()->create([
        'user_id' => $user->id,  // Ensure user_id is passed correctly
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'phone_number' => $validated['phone_number'],
        'address' => $validated['address'],
        'city' => $validated['city'],
    ]);

    return response()->json([
        'message' => 'Customer registered successfully',
        'user' => $user,
        'profile' => $profile
    ], 201);
}


public function registerServiceProvider(Request $request)
{
    // Validate the incoming request
    $validated = $request->validate([
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'phone_number' => 'required|string',
        'address' => 'required|string',
        'city' => 'required|string',
        'service_type' => 'array',
        'years_of_experience' => 'required|integer',
        'description' => 'required|string',
        'languages' => 'array',  // Array validation
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'  // Image validation
    ]);

    if ($request->hasFile('profile_image')) {
        // Store the image in the 'public/storage/images' directory
        $imagePath = $request->file('profile_image')->store('images', 'public');
    } else {
        // Default image path or null
        $imagePath = null;
    }


    // Create user with role 'ServiceProvider'
    $user = User::create([
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => UserRole::ServiceProvider->value,  // Using enum for role
    ]);



    // Create service provider profile
    $profile = $user->serviceProvider()->create([
        'user_id' => $user->id,  // Ensure user_id is passed correctly
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'phone_number' => $validated['phone_number'],
        'address' => $validated['address'],
        'city' => $validated['city'],
        'service_type' => json_encode($validated['service_type']),  // Store array as JSON
        'years_of_experience' => $validated['years_of_experience'],
        'description' => $validated['description'],
        'languages' => json_encode($validated['languages']),  // Store array as JSON
        'profile_image' => $imagePath  // Store image path in DB
    
    ]);

    // Return successful registration response
    return response()->json([
        'message' => 'Service Provider registered successfully',
        'user' => $user,
        'profile' => $profile
    ], 201);



}

    public function getCustomerProfile(Request $request) {
        $user = Auth::user(); 

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $profile = $user->customer;

        return response()->json($profile);
    }



public function updateCustomerProfile(Request $request)
{
    $user = Auth::user(); // Get authenticated user

    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Validate request
    $validated = $request->validate([
        'first_name' => 'nullable|string',
        'last_name' => 'nullable|string',
        'phone_number' => 'nullable|string',
        'address' => 'nullable|string',
        'city' => 'nullable|string',
    ]);

    $profile = $user->customer;

    if ($profile) {
        if (isset($validated['first_name'])) {
            $profile->first_name = $validated['first_name'];
        }
        if (isset($validated['last_name'])) {
            $profile->last_name = $validated['last_name'];
        }
        if (isset($validated['phone_number'])) {
            $profile->phone_number = $validated['phone_number'];
        }
        if (isset($validated['address'])) {
            $profile->address = $validated['address'];
        }
        if (isset($validated['city'])) {
            $profile->city = $validated['city'];
        }
        $profile->save(); // Save changes to the profile
    }

    return response()->json(['message' => 'Profile updated successfully']);
}


public function getServiceProviderProfile(Request $request)
{
    $user = Auth::user(); // Get authenticated user

    // Return the service provider's profile information
    return response()->json(['profile' => $user->serviceProvider], 200);
}


public function updateServiceProviderProfile(Request $request)
{
    $user = Auth::user(); // Get authenticated user

    // Validate request
    $validated = $request->validate([
        'first_name' => 'nullable|string',
        'last_name' => 'nullable|string',
        'phone_number' => 'nullable|string',
        'address' => 'nullable|string',
        'city' => 'nullable|string',
        'service_type' => 'nullable|array', // Optional array of service types
        'years_of_experience' => 'nullable|integer',
        'description' => 'nullable|string',
        'languages' => 'nullable|array', // Optional array of languages
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'  // Optional image validation
    ]);

    $profile = $user->serviceProvider;

    if ($profile) {
        if (isset($validated['first_name'])) {
            $profile->first_name = $validated['first_name'];
        }
        if (isset($validated['last_name'])) {
            $profile->last_name = $validated['last_name'];
        }
        if (isset($validated['phone_number'])) {
            $profile->phone_number = $validated['phone_number'];
        }
        if (isset($validated['address'])) {
            $profile->address = $validated['address'];
        }
        if (isset($validated['city'])) {
            $profile->city = $validated['city'];
        }
        if (isset($validated['service_type'])) {
            $profile->service_type = json_encode($validated['service_type']); // Store as JSON
        }
        if (isset($validated['years_of_experience'])) {
            $profile->years_of_experience = $validated['years_of_experience'];
        }
        if (isset($validated['description'])) {
            $profile->description = $validated['description'];
        }
        if (isset($validated['languages'])) {
            $profile->languages = json_encode($validated['languages']); // Store as JSON
        }
        if ($request->hasFile('profile_image')) {
            $profile->profile_image = $request->file('profile_image')->store('images', 'public');
        }
        $profile->save(); // Save changes to the profile
    }

    return response()->json(['message' => 'Service Provider profile updated successfully']);
}

}