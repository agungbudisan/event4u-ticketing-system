    <x-app-layout>
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-[#F8F4EC]">
        <!-- Sidebar backdrop (mobile only) -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-gray-600 bg-opacity-75 md:hidden"
             style="display: none;"></div>

        <!-- Mobile sidebar -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 z-40 w-64 bg-white md:hidden overflow-y-auto"
             style="display: none;">

            <div class="absolute top-0 right-0 p-1 -mr-12">
                <button @click="sidebarOpen = false" class="flex items-center justify-center w-10 h-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <i class="fas fa-times text-white"></i>
                    <span class="sr-only">Close sidebar</span>
                </button>
            </div>

            @include('components.sidebar')
        </div>

        <!-- Static sidebar for desktop -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="w-64">
                @include('components.sidebar')
            </div>
        </div>

        <!-- Page content -->
        <div class="flex flex-col flex-1 w-full overflow-hidden">
            <!-- Top navigation -->
            <div class="relative z-10 border-b border-[#E9E1D5] bg-[#F8F4EC] shadow-sm">
                <div class="flex h-16 items-center justify-between border-b border-[#E9E1D5] px-4">
                    <!-- Mobile menu button and logo -->
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="md:hidden text-gray-500 hover:text-gray-900 focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                            <span class="sr-only">Open sidebar</span>
                        </button>

                        <a href="{{ route('welcome') }}" class="ml-4 md:hidden flex items-center">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#E15B3F]">
                                <i class="fas fa-ticket-alt text-white text-sm"></i>
                            </div>
                            <span class="ml-2 text-lg font-semibold text-[#211F1C]">Event4U</span>
                        </a>
                    </div>

                    <!-- Page title -->
                    <div class="hidden md:block">
                        <h1 class="text-xl font-bold text-[#211F1C]">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <!-- Right section with button -->
                    <div class="flex items-center space-x-4">
                        <!-- User dropdown -->
                        <div class="relative" x-data="{ userMenuOpen: false }">
                            <button @click="userMenuOpen = !userMenuOpen" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-[#7B0015]" id="user-menu-button">
                                <span class="sr-only">Open user menu</span>
                                <div class="h-8 w-8 overflow-hidden rounded-full border-2 border-[#7B0015]">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=FFFFFF&background=7B0015" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                                </div>
                            </button>

                            <!-- Dropdown menu -->
                            <div x-show="userMenuOpen"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 @click.away="userMenuOpen = false"
                                 class="absolute right-0 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                 style="display: none;">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-edit mr-2 text-gray-500"></i> Edit Profil
                                </a>
                                <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-shopping-cart mr-2 text-gray-500"></i> Pesanan Saya
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2 text-gray-500"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <main class="flex-1 overflow-y-auto bg-[#F8F4EC] p-4 sm:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</x-app-layout>
