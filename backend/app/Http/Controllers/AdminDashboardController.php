<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\RatingandReview;

class AdminDashboardController extends Controller
{

    public function dashboard()
    {
        // Your logic for displaying the admin dashboard
        return view('admin.dashboard'); // Adjust this to your view file
    }

    public function showWorkers()
    {
        // Fetch all service providers grouped by their service type
        $serviceProviders = ServiceProvider::all()->groupBy('service_type');
        
        return view('admin.viewworkers', compact('serviceProviders'));
    }

    
    //! USER ANALYTICS 
    public function getTotalUsers() {
        $totalUsers = User::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalServiceProviders = User::where('role', 'service_provider')->count();
    
        return response()->json([
            'total' => $totalUsers,
            'customers' => $totalCustomers,
            'serviceProviders' => $totalServiceProviders
        ]);
    }

    public function getBookingHeatmap() {
        $bookingsByLocation = Appointment::select('location', DB::raw('COUNT(*) as total'))
                                    ->groupBy('location')
                                    ->get();

        return response()->json($bookingsByLocation);
    }


    public function getGrowthRate() {
        // Collect the growth rates for the past 6 months
        $growthData = [];
        for ($i = 5; $i >= 0; $i--) {
            $usersThisMonth = User::whereBetween('created_at', [now()->subMonths($i + 1), now()->subMonths($i)])->count();
            $usersLastMonth = User::whereBetween('created_at', [now()->subMonths($i + 2), now()->subMonths($i + 1)])->count();
            
            // Avoid division by zero
            if ($usersLastMonth > 0) {
                $growthRate = round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100, 2);
            } else {
                $growthRate = 0;  // If no users last month, growth rate is 0%
            }
            
            // Store data for each month
            $growthData[] = $growthRate;
        }
    
        return response()->json(['growthData' => $growthData]);
    }
    
    




    //! SERVICE PROVIDER ANALYTICS 


    public function getTopServiceCategories() {
        $topCategories = Appointment::select('service_type', DB::raw('COUNT(*) as total'))
                                ->groupBy('service_type')
                                ->orderBy('total', 'desc')
                                ->limit(5)
                                ->get();
    
        return response()->json($topCategories);
    }


    
    public function getTopServiceProviders()
    {
        $topProviders = ServiceProvider::select('service_providers.*')
            ->selectRaw('COUNT(appointments.id) as appointments_count') // Count the appointments
            ->join('appointments', 'appointments.service_provider_id', '=', 'service_providers.id') // Adjust the foreign key as necessary
            ->where('appointments.status', 'completed') // Filter for completed appointments
            ->groupBy('service_providers.id') // Group by service provider
            ->orderBy('appointments_count', 'desc') // Order by the count of completed appointments
            ->limit(5) // Limit to top 5
            ->get();
    
        return response()->json($topProviders);
    }
    


    //! BOOKING ANALYTICS 

    public function getTotalBookings() {
        $totalBookings = Appointment::count();
        $completedBookings = Appointment::where('status', 'completed')->count();
        $pendingBookings = Appointment::where('status', 'pending')->count();
    
        return response()->json([
            'total' => $totalBookings,
            'completed' => $completedBookings,
            'pending' => $pendingBookings
        ]);
    }

    public function getPeakBookingHours() {
        $peakHours = Appointment::whereNotNull('created_at')  // Ignore null values
                                ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as total'))
                                ->groupBy('hour')
                                ->orderBy('total', 'desc')
                                ->limit(5)
                                ->get();
        
        return response()->json($peakHours);
    }

    public function getAvgCompletionTime() {
        $avgCompletionTime = Appointment::where('status', 'completed')
                                        ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, completed_at)) as avg_time'))
                                        ->first();
    
        $totalMinutes = $avgCompletionTime->avg_time;
        $hours = floor($totalMinutes / 60); // Convert to hours
        $minutes = $totalMinutes % 60;      // Remaining minutes
    
        return response()->json([
            'avgCompletionTime' => [
                'hours' => $hours,
                'minutes' => $minutes
            ]
        ]);
    }
    

    public function getAvgResponseTime() {
        $avgResponseTime = Appointment::where('status', 'accepted')
                                  ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, accepted_at)) as avg_time'))
                                  ->first();
    
        return response()->json(['avgResponseTime' => $avgResponseTime->avg_time]);
    }

    //! REVENUE ANALYTICS 

    public function getTotalRevenue() {
        $totalRevenue = Appointment::where('status', 'completed')->sum('price');
    
        return response()->json(['totalRevenue' => $totalRevenue]);
    }

    //COOKED
    public function getRevenueByCategory() {
        $revenueByCategory = Appointment::select('service_type', DB::raw('SUM(price) as total'))
                                        ->groupBy('service_type')
                                        ->get();
    
        return response()->json($revenueByCategory);
    }
    


    public function getBookingForecast() {
        // Collect the booking data for the past 6 months
        $bookingData = Appointment::select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
                                   ->groupBy('month')
                                   ->orderBy('month', 'asc')  // Ensure months are ordered chronologically
                                   ->get();
    
        // If there's no data, return a message
        if ($bookingData->isEmpty()) {
            return response()->json(['message' => 'No data available for forecasting', 'forecast' => 0]);
        }
    
        // Calculate the moving average forecast
        $totalMonths = $bookingData->count();
        $totalBookings = $bookingData->sum('total');
        
        // Simple moving average: sum of bookings divided by the number of months
        $movingAverage = $totalBookings / $totalMonths;
    
        // Optionally, calculate a future projection (e.g., for the next month)
        $futureProjection = $movingAverage;  // Simple projection based on the moving average
        
        return response()->json([
            'forecast' => $futureProjection,
            'movingAverage' => $movingAverage
        ]);
    }

    public function index()
    {
        $pendingReviews = RatingandReview::where('moderation_status', 'pending')->get();
        return view('admin.moderation', compact('pendingReviews'));
    }

    public function approve($id)
    {
        $review = RatingandReview::findOrFail($id);
        $review->update(['moderation_status' => 'approved']);

        return redirect()->back()->with('success', 'Review has been approved.');
    }

    public function reject($id)
    {
        $review = RatingandReview::findOrFail($id);
        $review->update(['moderation_status' => 'rejected']);

        return redirect()->back()->with('success', 'Review has been rejected.');
    }


}    


