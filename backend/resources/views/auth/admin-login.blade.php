<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <!-- Email Input -->
            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-2 w-full border border-gray-300 rounded-md shadow-sm focus:border-[#FF7F66] focus:ring focus:ring-[#FF7F66] focus:ring-opacity-50"
                    type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <!-- Password Input -->
            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-2 w-full border border-gray-300 rounded-md shadow-sm focus:border-[#FF7F66] focus:ring focus:ring-[#FF7F66] focus:ring-opacity-50"
                    type="password" name="password" required autocomplete="current-password" />
            </div>

            <!-- Remember Me Checkbox -->
            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" class="text-[#FF7F66] focus:ring-[#FF7F66]"/>
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <!-- Forgot Password and Submit Button -->
            <div class="flex items-center justify-between mt-6">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-[#FF7F66] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FF7F66]" 
                       href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ml-4 bg-[#FF7F66] hover:bg-[#FF9F85] text-white font-semibold py-2 px-4 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FF7F66]">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
