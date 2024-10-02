@extends('layouts.admin')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Pending Reviews</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($pendingReviews as $review)
                <div class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-shadow duration-300 ease-in-out">
                    <div class="flex items-center mb-4">
                        <img src="https://via.placeholder.com/60" alt="User Picture" class="rounded-full w-12 h-12 object-cover mr-4">
                        <div>
                            <p class="text-xl font-bold">{{ $review->customer->first_name }} {{ $review->customer->last_name }}</p>
                            <p class="text-sm text-gray-500">{{ $review->created_at->format('F j, Y') }}</p>
                        </div>
                    </div>

                    <!-- Review Content -->
                    <div class="mb-4">
                        <p class="text-gray-700"><strong>Service Provider:</strong> {{ $review->serviceprovider->first_name }} {{ $review->serviceprovider->last_name }}</p>
                        <p class="text-gray-700"><strong>Service Type:</strong> {{ $review->appointment->service_type }}</p>
                        <p class="text-gray-700"><strong>Review:</strong> "{{ $review->review }}"</p>
                        <p class="text-gray-700"><strong>Rating:</strong> 
                            <span class="text-yellow-400">
                                @for ($i = 1; $i <= $review->rating; $i++)
                                    ★
                                @endfor
                                @for ($i = $review->rating + 1; $i <= 5; $i++)
                                    ☆
                                @endfor
                            </span>
                        </p>
                    </div>

                    <!-- Approve or Reject Buttons -->
                    <div class="flex justify-between">
                        <form action="{{ route('review.approve', $review->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Approve
                            </button>
                        </form>
                        <form action="{{ route('review.reject', $review->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Reject
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
