<x-guest-layout>
    <div class="flex min-h-screen">
        <!-- Form Section -->
        <div class="flex w-full flex-col items-center justify-center bg-[#F8F4EC] px-8 py-12 md:w-3/4">
            <div class="mb-8 w-full max-w-sm">
                <a href="{{ route('welcome') }}" class="mb-8 flex items-center gap-2 text-sm font-bold text-[#211F1C]"><span class="h-2.5 w-2.5 rounded-full bg-[#E15B3F]"></span> Event4U</a>
                <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-[#E15B3F]">Start your journey</p>
                <h2 class="text-3xl font-bold tracking-tight text-[#211F1C]">Create your account</h2>
            </div>

            <form method="POST" action="{{ route('register') }}" class="w-full max-w-sm space-y-1">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="mb-4">
                    <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="new-password" placeholder="Password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <x-text-input id="password_confirmation" class="block w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Submit -->
                <div class="mb-4">
                    <button type="submit"
                        class="w-full rounded-full bg-[#7B0015] py-3 font-bold text-white transition hover:bg-[#E15B3F]">
                        Sign Up
                    </button>
                </div>

                <!-- Link to login -->
                <p class="text-sm text-center text-gray-700" style="font-family: Constantia, Georgia, serif;">
                    Already have account?
                    <a href="{{ route('login') }}" class="font-bold text-[#7B0015] hover:text-[#E15B3F]">Sign in</a>
                </p>
            </form>
        </div>

        <!-- Right Banner -->
        <div class="w-1/4 relative overflow-hidden flex flex-col justify-center items-center p-8 bg-[#7c0a02] text-white">
            <!-- Text Layer -->
            <div class="z-10 text-center">
                <h1 class="text-6xl font-bold"  style="font-family: Constantia, Georgia, serif; line-height: 1.4;">WELCOME</h1>
                <p class="mt-2 text-4xl italic" style="font-family: 'Rock Salt', cursive; line-height: 1.4; letter-spacing: 2px;">Are You Ready<br>For Fun?</p>
            </div>

            <!-- Camera Icon Positioned Bottom Right -->
            <img src="{{ asset('images/camera-icon.png') }}"
                alt="Camera Icon"
                class="absolute bottom-6 right-6 w-[45%] max-w-[180px] sm:max-w-[200px] md:max-w-[220px] z-10" />

            <!-- Background Pattern -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/banner-pattern.png') }}"
                    alt="Pattern"
                    class="object-cover w-full h-full opacity-50" />
            </div>
        </div>
    </div>
</x-guest-layout>
