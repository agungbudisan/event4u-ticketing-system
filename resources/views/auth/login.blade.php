<x-guest-layout>
    <div class="flex min-h-screen">
        <!-- Left Side (Login Form) -->
        <div class="flex w-full items-center justify-center bg-[#F8F4EC] md:w-3/4">
            <div class="w-full max-w-md space-y-6 p-8 sm:p-12">
                <div class="mb-10">
                    <a href="{{ route('welcome') }}" class="mb-8 flex items-center gap-2 text-sm font-bold text-[#211F1C]">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#E15B3F]"></span> Event4U
                    </a>
                    <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-[#E15B3F]">Welcome back</p>
                    <h1 class="text-3xl font-bold tracking-tight text-[#211F1C]">Sign in to your account</h1>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            required
                            autofocus
                            :value="old('email')"
                            placeholder="Email"
                            class="block w-full rounded-xl border-[#E9E1D5] bg-white px-4 py-3 text-sm shadow-sm placeholder-gray-500 focus:border-[#E15B3F] focus:ring-[#E15B3F]"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            placeholder="Password"
                            class="block w-full rounded-xl border-[#E9E1D5] bg-white px-4 py-3 text-sm shadow-sm placeholder-gray-500 focus:border-[#E15B3F] focus:ring-[#E15B3F]"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me + Forgot -->
                    <div class="flex items-center justify-between text-sm">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="mr-2 text-[#510101]" style="font-family: Constantia, Georgia, serif;">
                            Remember me
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-[#510101] hover:underline" href="{{ route('password.request') }}" style="font-family: Constantia, Georgia, serif;">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Sign In Button -->
                    <div class="flex flex-col items-center mt-4">
                        <button type="submit" class="w-full rounded-full bg-[#7B0015] py-3 font-bold text-white transition hover:bg-[#E15B3F]" >
                            Sign In
                        </button>
                        <p class="mt-4 text-sm text-gray-700" style="font-family: Constantia, Georgia, serif;">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="font-bold text-[#7B0015] hover:text-[#E15B3F]">Register</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Side (Image) -->
        <div class="hidden md:block md:w-1/4 relative">
            <img src="{{ asset('images/moon-banner.png') }}" alt="Login Image" class="h-full w-full object-cover">
        </div>
    </div>
</x-guest-layout>
