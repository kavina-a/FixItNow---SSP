@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Service Providers by Category</h1>

        @foreach ($serviceProviders as $service_type => $providers)
            <h2 class="mt-5">{{ $service_type }}</h2>
            <div class="row">
                @foreach ($providers as $provider)
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">{{ $provider->first_name }}</h5>
                                <p class="card-text">Email: {{ $provider->email }}</p>
                                <p class="card-text">Phone: {{ $provider->phone ?? 'Not available' }}</p>
                                <p class="card-text">Description: {{ $provider->description ?? 'No description available' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endsection
