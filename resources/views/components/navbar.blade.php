<nav class="bg-[#211F1C] text-white py-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center px-4 sm:px-6">
        <!-- Logo -->
        <a href="{{ route('welcome') }}" class="flex items-center">
            <span class="mr-2 h-2.5 w-2.5 rounded-full bg-[#E15B3F]"></span>
            <h1 class="text-[#F8F4EC] text-2xl font-bold tracking-tight">Event4U</h1>
        </a>

        <!-- Mobile menu button -->
        <div class="md:hidden">
            <button id="mobile-menu-button" class="text-white focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center space-x-2">
            <a href="{{ route('welcome') }}" class="rounded-full px-4 py-2 text-[#F8F4EC]/80 hover:bg-white/10 hover:text-white transition-colors">Home</a>
            <a href="{{ route('events.index') }}" class="rounded-full px-4 py-2 text-[#F8F4EC]/80 hover:bg-white/10 hover:text-white transition-colors">Events</a>

            <!-- Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="ml-2 flex items-center rounded-full border border-white/20 px-4 py-2 text-[#F8F4EC]/90 hover:bg-white/10 hover:text-white transition-colors focus:outline-none">
                    <span class="mr-1">
                        <i class="fas fa-user-circle text-xl"></i>
                    </span>
                    @auth
                        <span>{{ Auth::user()->name }}</span>
                    @else
                        <span>Sign In?</span>
                    @endauth
                    <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>

                <!-- Dropdown menu -->
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                    @auth
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-tachometer-alt mr-2"></i> Admin Dashboard
                            </a>
                        @elseif(Auth::user()->role === 'user')
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-home mr-2"></i> Dashboard
                            </a>
                        @endif
                        <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-history mr-2"></i> Order History
                        </a>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-edit mr-2"></i> Profile
                        </a>
                        <div class="border-t border-gray-100"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-sign-in-alt mr-2"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-plus mr-2"></i> Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu (Hidden by default) -->
    <div id="mobile-menu" class="hidden md:hidden mt-2 px-4 pt-2 pb-4 bg-[#7B0015] border-t border-[#9B1D35]">
        <a href="{{ route('welcome') }}" class="block py-2 text-[#F7ECAC] hover:text-white">Home</a>
        <a href="{{ route('events.index') }}" class="block py-2 text-[#F7ECAC] hover:text-white">Events</a>

        <div class="pt-2 mt-2 border-t border-[#9B1D35]">
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="block py-2 text-[#F7ECAC] hover:text-white">
                        <i class="fas fa-tachometer-alt mr-2"></i> Admin Dashboard
                    </a>
                @elseif(Auth::user()->role === 'user')
                    <a href="{{ route('dashboard') }}" class="block py-2 text-[#F7ECAC] hover:text-white">
                        <i class="fas fa-home mr-2"></i> Dashboard
                    </a>
                @endif
                <a href="{{ route('orders.index') }}" class="block py-2 text-[#F7ECAC] hover:text-white">
                    <i class="fas fa-history mr-2"></i> Order History
                </a>
                <a href="{{ route('profile.edit') }}" class="block py-2 text-[#F7ECAC] hover:text-white">
                    <i class="fas fa-user-edit mr-2"></i> Profile
                </a>
                <form method="POST" action="{{ route('logout') }}" class="block py-2">
                    @csrf
                    <button type="submit" class="text-[#F7ECAC] hover:text-white">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block py-2 text-[#F7ECAC] hover:text-white">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </a>
                <a href="{{ route('register') }}" class="block py-2 text-[#F7ECAC] hover:text-white">
                    <i class="fas fa-user-plus mr-2"></i> Register
                </a>
            @endauth
        </div>
    </div>
</nav>

<script>
    // JavaScript to toggle the mobile menu
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>
