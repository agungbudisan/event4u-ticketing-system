<div class="sidebar flex h-full flex-col bg-[#211F1C] text-white">
    <div class="flex h-16 items-center justify-center border-b border-white/10 px-4">
        <a href="{{ route('welcome') }}" class="flex items-center space-x-2">
            {{-- <div class="h-8 w-8 rounded-full bg-[#7B0015] flex items-center justify-center">
                <i class="fas fa-ticket-alt text-white text-sm"></i>
            </div> --}}
            <span class="mr-2 h-2.5 w-2.5 rounded-full bg-[#E15B3F]"></span>
            <span class="text-lg font-bold tracking-tight text-[#F8F4EC]">Event4U</span>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto p-4">
        <p class="mb-3 px-4 text-[0.65rem] font-bold uppercase tracking-[0.2em] text-white/40">Workspace</p>
        <div class="flex flex-col space-y-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center rounded-xl px-4 py-3 transition {{ request()->routeIs('dashboard') ? 'bg-[#E15B3F] text-white shadow-lg' : 'text-white/65 hover:bg-white/10 hover:text-white' }}">
                <i class="fas fa-home w-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-[#7B0015]' }}"></i>
                <span class="ml-3">Dashboard</span>
            </a>

            <a href="{{ route('orders.index') }}"
               class="flex items-center rounded-xl px-4 py-3 transition {{ request()->routeIs('orders.*') ? 'bg-[#E15B3F] text-white shadow-lg' : 'text-white/65 hover:bg-white/10 hover:text-white' }}">
                <i class="fas fa-shopping-cart w-5 {{ request()->routeIs('orders.*') ? 'text-white' : 'text-[#7B0015]' }}"></i>
                <span class="ml-3">Pesanan Saya</span>
            </a>

            <a href="{{ route('profile.edit') }}"
               class="flex items-center rounded-xl px-4 py-3 transition {{ request()->routeIs('profile.*') ? 'bg-[#E15B3F] text-white shadow-lg' : 'text-white/65 hover:bg-white/10 hover:text-white' }}">
                <i class="fas fa-user w-5 {{ request()->routeIs('profile.*') ? 'text-white' : 'text-[#7B0015]' }}"></i>
                <span class="ml-3">Profil Saya</span>
            </a>
        </div>

        <div class="mt-4 border-t border-white/10 pt-4">
            <a href="{{ route('events.index') }}" class="flex items-center rounded-xl px-4 py-3 text-white/65 transition hover:bg-white/10 hover:text-white">
                <i class="fas fa-search w-5 text-[#7B0015]"></i>
                <span class="ml-3">Jelajahi Acara</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="flex w-full items-center rounded-xl px-4 py-3 text-white/65 transition hover:bg-white/10 hover:text-white">
                    <i class="fas fa-sign-out-alt w-5 text-[#7B0015]"></i>
                    <span class="ml-3">Keluar</span>
                </button>
            </form>
        </div>
    </div>

    <!-- User info at bottom of sidebar - only shown on desktop -->
    <div class="border-t border-white/10 bg-black/10 p-4">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                <div class="h-9 w-9 rounded-full overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=FFFFFF&background=7B0015"
                         alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="truncate text-sm font-medium text-white">
                    {{ Auth::user()->name }}
                </p>
                <p class="truncate text-xs text-white/50">
                    {{ Auth::user()->email }}
                </p>
            </div>
            <a href="{{ route('profile.edit') }}" class="text-white/50 hover:text-white">
                <i class="fas fa-cog"></i>
            </a>
        </div>
    </div>
</div>
