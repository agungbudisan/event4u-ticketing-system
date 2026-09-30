<x-dashboard-layout>
    @section('page-title', 'Dashboard')

    <div class="space-y-4 sm:space-y-6">
        <!-- Welcome Banner -->
        <div class="relative overflow-hidden rounded-2xl bg-[#211F1C] shadow-[0_12px_35px_rgba(33,31,28,0.15)]">
            <div class="p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between">
                <div class="flex flex-col sm:flex-row sm:items-center">
                    <div class="h-12 w-12 overflow-hidden rounded-full border-2 border-[#E15B3F]">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=FFFFFF&background=bg-white/20"
                         alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Selamat datang, {{ Auth::user()->name }}!</h3>
                        <p class="text-white/80">Terima kasih telah menggunakan layanan Event4U</p>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('events.index') }}" class="inline-flex items-center rounded-full bg-[#F7ECAC] px-5 py-2.5 text-sm font-bold text-[#211F1C] transition-colors hover:bg-white">
                        <i class="fas fa-ticket-alt mr-2"></i>
                        Jelajahi Event
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Orders Stats -->
            <div class="overflow-hidden rounded-2xl border border-[#E9E1D5] bg-white shadow-sm">
                <div class="p-4 sm:p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Pemesanan</p>
                            @php
                                $totalOrders = \App\Models\Order::where('user_id', Auth::id())->count();
                            @endphp
                            <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalOrders }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                <span class="text-green-600">
                                    <i class="fas fa-arrow-up text-xs"></i>
                                    5%
                                </span>
                                vs bulan lalu
                            </p>
                        </div>
                        <div class="h-12 w-12 sm:h-14 sm:w-14 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-blue-500 text-lg sm:text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="border-t border-[#E9E1D5] bg-[#F8F4EC] px-4 py-2">
                    <a href="{{ route('orders.index') }}" class="text-sm font-medium text-[#7B0015] hover:text-[#950019] flex items-center">
                        Lihat semua pesanan
                        <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="overflow-hidden rounded-2xl border border-[#E9E1D5] bg-white shadow-sm">
                <div class="p-4 sm:p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Acara Mendatang</p>
                            @php
                                $upcomingEvents = \App\Models\Order::where('user_id', Auth::id())
                                    ->whereHas('ticket.event', function($query) {
                                        $query->where('start_event', '>', now());
                                    })
                                    ->count();
                            @endphp
                            <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $upcomingEvents }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                @if($upcomingEvents > 0)
                                <span class="text-[#7B0015]">
                                    Event terdekat:
                                    @php
                                        $nextEvent = \App\Models\Order::where('user_id', Auth::id())
                                            ->whereHas('ticket.event', function($query) {
                                                $query->where('start_event', '>', now());
                                            })
                                            ->with(['ticket.event' => function($query) {
                                                $query->orderBy('start_event', 'asc');
                                            }])
                                            ->first();
                                    @endphp
                                    {{ $nextEvent && $nextEvent->ticket && $nextEvent->ticket->event ? $nextEvent->ticket->event->start_event->format('d M') : '' }}
                                </span>
                                @else
                                <span>Tidak ada acara</span>
                                @endif
                            </p>
                        </div>
                        <div class="h-12 w-12 sm:h-14 sm:w-14 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-green-500 text-lg sm:text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="border-t border-[#E9E1D5] bg-[#F8F4EC] px-4 py-2">
                    <a href="#upcoming-events" class="text-sm font-medium text-[#7B0015] hover:text-[#950019] flex items-center">
                        Lihat jadwal acara
                        <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Active Tickets -->
            <div class="overflow-hidden rounded-2xl border border-[#E9E1D5] bg-white shadow-sm">
                <div class="p-4 sm:p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tiket Aktif</p>
                            @php
                                $activeTickets = \App\Models\Order::where('user_id', Auth::id())
                                    ->whereHas('payment', function($query) {
                                        $query->where('status', 'completed');
                                    })
                                    ->count();
                            @endphp
                            <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $activeTickets }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                <span class="{{ $activeTickets > 0 ? 'text-green-600' : 'text-gray-500' }}">
                                    <i class="fas fa-{{ $activeTickets > 0 ? 'check-circle' : 'info-circle' }} text-xs"></i>
                                    {{ $activeTickets > 0 ? 'Siap digunakan' : 'Tidak ada tiket' }}
                                </span>
                            </p>
                        </div>
                        <div class="h-12 w-12 sm:h-14 sm:w-14 rounded-full bg-purple-100 flex items-center justify-center">
                            <i class="fas fa-ticket-alt text-purple-500 text-lg sm:text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="border-t border-[#E9E1D5] bg-[#F8F4EC] px-4 py-2">
                    <a href="{{ route('orders.index') }}" class="text-sm font-medium text-[#7B0015] hover:text-[#950019] flex items-center">
                        Lihat e-ticket
                        <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Completed Events -->
            <div class="overflow-hidden rounded-2xl border border-[#E9E1D5] bg-white shadow-sm">
                <div class="p-4 sm:p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Acara Dihadiri</p>
                            @php
                                $completedEvents = \App\Models\Order::where('user_id', Auth::id())
                                    ->whereHas('payment', function($query) {
                                        $query->where('status', 'completed');
                                    })
                                    ->whereHas('ticket.event', function($query) {
                                        $query->where('start_event', '<', now());
                                    })
                                    ->count();
                            @endphp
                            <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $completedEvents }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                @if($completedEvents > 0)
                                <span class="text-indigo-600">
                                    <i class="fas fa-star text-xs"></i>
                                    Terakhir:
                                    @php
                                        $lastEvent = \App\Models\Order::where('user_id', Auth::id())
                                            ->whereHas('payment', function($query) {
                                                $query->where('status', 'completed');
                                            })
                                            ->whereHas('ticket.event', function($query) {
                                                $query->where('start_event', '<', now());
                                            })
                                            ->with(['ticket.event' => function($query) {
                                                $query->orderBy('start_event', 'desc');
                                            }])
                                            ->first();
                                    @endphp
                                    {{ $lastEvent && $lastEvent->ticket && $lastEvent->ticket->event ? $lastEvent->ticket->event->title : '' }}
                                </span>
                                @else
                                <span>Belum ada</span>
                                @endif
                            </p>
                        </div>
                        <div class="h-12 w-12 sm:h-14 sm:w-14 rounded-full bg-indigo-100 flex items-center justify-center">
                            <i class="fas fa-history text-indigo-500 text-lg sm:text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="border-t border-[#E9E1D5] bg-[#F8F4EC] px-4 py-2">
                    <a href="#" class="text-sm font-medium text-[#7B0015] hover:text-[#950019] flex items-center">
                        Lihat riwayat event
                        <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="overflow-hidden rounded-2xl border border-[#E9E1D5] bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-[#E9E1D5] px-4 py-4 sm:px-6">
                <div class="flex items-center">
                    <i class="fas fa-receipt text-[#7B0015] mr-2"></i>
                    <h3 class="text-base sm:text-lg font-medium text-gray-900">Pesanan Terbaru</h3>
                </div>
                <a href="{{ route('orders.index') }}" class="text-sm text-[#7B0015] hover:underline flex items-center">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="p-4 sm:p-6">
                @php
                    $recentOrders = \App\Models\Order::where('user_id', Auth::id())
                        ->with(['ticket.event', 'payment'])
                        ->latest()
                        ->take(3)
                        ->get();
                @endphp

                @if($recentOrders->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($recentOrders as $order)
                            <div class="py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
                                <div class="flex items-start">
                                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-lg bg-gray-100 flex-shrink-0 overflow-hidden mr-3">
                                        @if($order->ticket && $order->ticket->event && $order->ticket->event->thumbnail)
                                            <img src="{{ asset('storage/' . $order->ticket->event->thumbnail) }}" alt="{{ $order->ticket->event->title }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center bg-gray-200">
                                                <i class="fas fa-calendar text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $order->ticket && $order->ticket->event ? $order->ticket->event->title : 'Acara' }}</p>
                                        <div class="text-sm text-gray-500">
                                            <div class="flex flex-wrap gap-2 sm:gap-3">
                                                <span>{{ $order->ticket ? $order->ticket->ticket_class : '' }}</span>
                                                <span class="hidden sm:inline mx-1">•</span>
                                                <span>{{ $order->quantity }}x tiket</span>
                                                <span class="hidden sm:inline mx-1">•</span>
                                                <span>
                                                    @if(isset($order->payment) && $order->payment->status == 'completed')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-check-circle mr-1"></i> Pembayaran Selesai
                                                        </span>
                                                    @elseif(isset($order->payment) && $order->payment->status == 'pending')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            <i class="fas fa-clock mr-1"></i> Menunggu Pembayaran
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            <i class="fas fa-exclamation-circle mr-1"></i> Belum Dibayar
                                                        </span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </span>
                                    <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm text-gray-700">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="h-16 w-16 sm:h-20 sm:w-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-ticket-alt text-gray-400 text-xl sm:text-2xl"></i>
                        </div>
                        <p class="text-gray-500 mb-4">Anda belum memiliki pesanan</p>
                        <a href="{{ route('events.index') }}" class="inline-flex items-center px-4 py-2 bg-[#7B0015] hover:bg-[#950019] text-white font-medium rounded-lg transition duration-300">
                            <i class="fas fa-search mr-2"></i> Jelajahi Acara
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Upcoming Events -->
        <div id="upcoming-events" class="overflow-hidden rounded-2xl border border-[#E9E1D5] bg-white shadow-sm">
            <div class="flex items-center border-b border-[#E9E1D5] px-4 py-4 sm:px-6">
                <i class="fas fa-calendar-alt text-[#7B0015] mr-2"></i>
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Acara yang Akan Datang</h3>
            </div>

            <div class="p-4 sm:p-6">
                @php
                    $upcomingEvents = \App\Models\Event::where('start_event', '>', now())
                        ->take(3)
                        ->get();
                @endphp

                @if($upcomingEvents->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        @foreach($upcomingEvents as $event)
                            <div class="border border-gray-200 rounded-lg overflow-hidden flex flex-col h-full transition-shadow hover:shadow-md">
                                <div class="h-32 sm:h-40 overflow-hidden relative">
                                    <img src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                                    <div class="absolute top-2 right-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-black bg-opacity-60 text-white">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ now()->diffForHumans($event->start_event, ['parts' => 1]) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="p-3 sm:p-4 flex-1">
                                    <h4 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $event->title }}</h4>
                                    <div class="text-sm text-gray-500 space-y-2">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-day text-[#7B0015] mr-2 w-4 text-center"></i>
                                            <span>{{ $event->start_event->format('d F Y') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock text-[#7B0015] mr-2 w-4 text-center"></i>
                                            <span>{{ $event->start_event->format('H:i') }} WIB</span>
                                        </div>
                                        <div class="flex items-start">
                                            <i class="fas fa-map-marker-alt text-[#7B0015] mr-2 w-4 text-center mt-1"></i>
                                            <span class="line-clamp-2">{{ $event->location }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 sm:p-4 border-t border-gray-200">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="text-sm text-gray-500">Mulai dari</div>
                                        <div class="text-[#7B0015] font-bold">
                                            @php
                                                $cheapestTicket = \App\Models\Ticket::where('event_id', $event->id)
                                                    ->orderBy('price', 'asc')
                                                    ->first();
                                            @endphp
                                            Rp {{ number_format($cheapestTicket ? $cheapestTicket->price : 0, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <a href="{{ route('events.show', $event) }}" class="block w-full py-2 bg-[#7B0015] hover:bg-[#950019] text-white text-center font-medium rounded-lg transition">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-6">
                        <a href="{{ route('events.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-black text-white font-medium rounded-lg transition">
                            <i class="fas fa-calendar-alt mr-2"></i> Lihat Semua Event
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="h-16 w-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-calendar-times text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-gray-500 mb-4">Tidak ada acara mendatang saat ini</p>
                        <a href="{{ route('events.index') }}" class="inline-flex items-center px-4 py-2 bg-[#7B0015] hover:bg-[#950019] text-white font-medium rounded-lg transition">
                            <i class="fas fa-search mr-2"></i> Jelajahi Event
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-dashboard-layout>
