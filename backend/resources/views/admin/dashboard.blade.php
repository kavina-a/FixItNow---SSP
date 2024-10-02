@extends('layouts.admin')

@section('content')
<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->


   <!-- Main Content Area -->
   <div class="flex-grow p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 overflow-y-auto">
    <!-- Total Users Card -->
    <div class="card bg-blue-50 border border-blue-400 rounded-lg p-6 shadow-lg">
        <h3 class="text-xl font-bold text-gray-700">Total Users</h3>
        <canvas id="totalUsersChart" class="w-full h-40"></canvas>
    </div>

    <!-- Growth Rate Card -->
    <div class="card bg-white border border-red-400 rounded-lg p-6 shadow-lg">
        <h3 class="text-xl font-bold text-gray-700">Growth Rate</h3>
        <canvas id="growthRateChart" class="w-full h-40"></canvas>
    </div>

    <!-- Top Service Categories Card -->
    <div class="card bg-white border border-gray-300 rounded-lg p-6 shadow-lg">
        <h3 class="text-xl font-bold text-gray-700">Top Service Categories</h3>
        <canvas id="topServiceCategoriesChart" class="w-full h-40"></canvas>
    </div>

    <!-- Top Service Providers Card -->
    <div class="card bg-white border border-gray-300 rounded-lg p-6 shadow-lg">
        <h3 class="text-xl font-bold text-gray-700">Top Service Providers</h3>
        <canvas id="topServiceProvidersChart" class="w-full h-40"></canvas>
    </div>


    <!-- Total Bookings Card -->
    <div class="card bg-white border border-green-400 rounded-lg p-6 shadow-lg">
        <h3 class="text-xl font-bold text-gray-700">Total Bookings</h3>
        <canvas id="totalBookingsChart" class="w-full h-40"></canvas>
    </div>

    <!-- Peak Booking Hours Card -->
    <div class="card bg-white border border-blue-400 rounded-lg p-6 shadow-lg">
        <h3 class="text-xl font-bold text-gray-700">Peak Booking Hours</h3>
        <canvas id="peakBookingHoursChart" class="w-full h-40"></canvas>
    </div>

    <!-- Avg Completion Time Card -->
    <div class="card bg-white border border-indigo-400 rounded-lg p-6 shadow-lg">
        <h3 class="text-xl font-bold text-gray-700">Average Completion Time</h3>
        <canvas id="avgCompletionTimeChart" class="w-full h-40"></canvas>
    </div>

    {{-- <!-- Total Revenue Card -->
    <div class="card bg-white border border-teal-400 rounded-lg p-6 shadow-lg">
        <h3 class="text-xl font-bold text-gray-700">Total Revenue</h3>
        <div id="totalRevenue" class="text-3xl font-bold text-teal-500">0.00</div>
    </div>

    <!-- Revenue by Category Card -->
    <div class="card bg-white border border-pink-400 rounded-lg p-6 shadow-lg">
        <h3 class="text-xl font-bold text-gray-700">Revenue by Category</h3>
        <canvas id="revenueByCategoryChart" class="w-full h-40"></canvas>
    </div> --}}

    <!-- Booking Forecast Card -->
    <div class="card bg-white border border-gray-300 rounded-lg p-6 shadow-lg">
        <h3 class="text-xl font-bold text-gray-700">Booking Forecast</h3>
        <canvas id="bookingForecastChart" class="w-full h-40"></canvas>
    </div>
</div>
</div>
@endsection

<!-- Include Vite assets -->
@vite(['resources/css/admin-dashboard.css', 'resources/js/admin-dashboard.js'])