<x-dashboard-layout>
    @section('page-title', 'Profil Saya')

    <div class="space-y-4 sm:space-y-6">
        <!-- Profile Info Card -->
        <div class="overflow-hidden rounded-2xl border border-[#E9E1D5] bg-white shadow-sm">
            <div class="p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center">
                    <div class="flex-shrink-0 mb-4 sm:mb-0 sm:mr-6 flex justify-center">
                        <div class="h-24 w-24 overflow-hidden rounded-full border-2 border-[#E15B3F] bg-[#F1ECE3] sm:h-32 sm:w-32">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=FFFFFF&background=7B0015"
                                alt="{{ $user->name }}" class="h-full w-full object-cover">
                        </div>
                    </div>
                    <div class="text-center sm:text-left">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        <div class="mt-2 flex flex-wrap justify-center sm:justify-start gap-2">
                            <span class="inline-flex items-center rounded-full bg-[#7B0015] px-3 py-1 text-xs font-medium text-white">
                                {{ ucfirst($user->role) }}
                            </span>
                            <span class="inline-flex items-center rounded-full bg-[#F1ECE3] px-3 py-1 text-xs font-medium text-[#211F1C]">
                                Member sejak {{ $user->created_at->format('M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Aktivitas -->
        <div class="overflow-hidden rounded-2xl border border-[#E9E1D5] bg-white shadow-sm">
            <div class="border-b border-gray-200 px-4 sm:px-6 py-4">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Ringkasan Aktivitas</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Total Pemesanan -->
                    <div class="rounded-xl bg-[#F8F4EC] p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Pemesanan</p>
                                @php
                                    $totalOrders = \App\Models\Order::where('user_id', $user->id)->count();
                                @endphp
                                <p class="text-lg sm:text-xl font-semibold">{{ $totalOrders }}</p>
                            </div>
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-blue-500"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Acara Dihadiri -->
                    <div class="rounded-xl bg-[#F8F4EC] p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Acara Dihadiri</p>
                                @php
                                    $attendedEvents = \App\Models\Order::where('user_id', $user->id)
                                        ->whereHas('payment', function($query) {
                                            $query->where('status', 'completed');
                                        })
                                        ->whereHas('ticket.event', function($query) {
                                            $query->where('start_event', '<', now());
                                        })
                                        ->count();
                                @endphp
                                <p class="text-lg sm:text-xl font-semibold">{{ $attendedEvents }}</p>
                            </div>
                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-500"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Acara Mendatang -->
                    <div class="rounded-xl bg-[#F8F4EC] p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Acara Mendatang</p>
                                @php
                                    $upcomingEvents = \App\Models\Order::where('user_id', $user->id)
                                        ->whereHas('payment', function($query) {
                                            $query->where('status', 'completed');
                                        })
                                        ->whereHas('ticket.event', function($query) {
                                            $query->where('start_event', '>', now());
                                        })
                                        ->count();
                                @endphp
                                <p class="text-lg sm:text-xl font-semibold">{{ $upcomingEvents }}</p>
                            </div>
                            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-calendar text-purple-500"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Akun -->
        <div class="overflow-hidden rounded-2xl border border-[#E9E1D5] bg-white shadow-sm">
            <div class="border-b border-gray-200 px-4 sm:px-6 py-4">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Informasi Akun</h3>
            </div>
            <div class="p-4 sm:p-6">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password -->
        <div class="overflow-hidden rounded-2xl border border-[#E9E1D5] bg-white shadow-sm">
            <div class="border-b border-gray-200 px-4 sm:px-6 py-4">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Ubah Password</h3>
            </div>
            <div class="p-4 sm:p-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Hapus Akun -->
        <div class="overflow-hidden rounded-2xl border border-[#E9E1D5] bg-white shadow-sm">
            <div class="border-b border-gray-200 px-4 sm:px-6 py-4">
                <h3 class="text-base sm:text-lg font-medium text-red-600">Hapus Akun</h3>
            </div>
            <div class="p-4 sm:p-6">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-dashboard-layout>
