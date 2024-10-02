<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css'])
</head>

<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-1/6 bg-[#FF7F66] text-white flex flex-col items-center py-6 shadow-lg">
            <!-- User profile icon -->
            <div class="mb-8 flex items-center justify-center">
                <img src="{{ asset('storage/images/log.png') }}" alt="FixItNow Logo"
                    class="w-24 h-24 object-cover rounded-full border-4 border-white shadow-lg" />
            </div>

            <!-- Navigation -->
            <nav class="flex flex-col w-full px-4 space-y-4">
                <!-- Analytics (active link) -->
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center space-x-4 py-4 px-6 text-white transition duration-300 ease-in-out hover:bg-white hover:text-[#FF7F66] rounded-lg hover:shadow-lg focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12l2-2 4 4L21 4" />
                        <path d="M2 12l7 7 9-9" />
                    </svg>
                    <span class="text-lg font-semibold">Analytics</span>
                </a>

                <!-- Moderation -->
                <a href="{{ route('reviews.moderation') }}"
                    class="flex items-center space-x-4 py-4 px-6 text-white transition duration-300 ease-in-out hover:bg-white hover:text-[#FF7F66] rounded-lg hover:shadow-lg focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 5h4l-1.5 1.5M9 5H5L6.5 6.5M3 7v10a4 4 0 004 4h10a4 4 0 004-4V7M16 5H8" />
                    </svg>
                    <span class="text-lg font-semibold">Moderation</span>
                </a>

                <!-- Users -->
                <a href="{{ route('admin.spview') }}"
                    class="flex items-center space-x-4 py-4 px-6 text-white transition duration-300 ease-in-out hover:bg-white hover:text-[#FF7F66] rounded-lg hover:shadow-lg focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="7" r="4" />
                        <path d="M16 14v1a5 5 0 01-5 5h-2a5 5 0 01-5-5v-1" />
                    </svg>
                    <span class="text-lg font-semibold">Users</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-grow p-8 overflow-y-auto">
            @yield('content') <!-- This will insert the content of each page -->
        </div>
    </div>

    <!-- Include Vite-compiled JavaScript -->
    @vite(['resources/js/app.js'])
</body>

</html>
