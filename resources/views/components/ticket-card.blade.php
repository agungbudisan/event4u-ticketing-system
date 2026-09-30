@props(['ticket'])

<div class="overflow-hidden rounded-2xl border-2 bg-white shadow-sm {{ now() >= $ticket->event->start_sale && now() <= $ticket->event->end_sale ? 'border-[#E15B3F]' : 'border-[#E9E1D5]' }}">
    <div class="p-6 grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-4">
        <div>
            <h3 class="text-xl font-bold {{ now() >= $ticket->event->start_sale && now() <= $ticket->event->end_sale ? 'text-[#7B0015]' : 'text-gray-600' }}">{{ $ticket->ticket_class }}</h3>
            <p class="text-sm text-gray-600 mt-1">{{ $ticket->description ?? 'Tiket ' . $ticket->ticket_class }}</p>
            <div class="mt-2 flex items-center">
                @php
                    $isSaleOpen = now() >= $ticket->event->start_sale && now() <= $ticket->event->end_sale;
                @endphp

                @if($ticket->quota_avail > 0 && $isSaleOpen)
                    <span class="flex items-center space-x-1 text-sm text-green-600">
                        <i class="fas fa-circle text-xs"></i>
                        <span>{{ $ticket->quota_avail }} tiket tersedia</span>
                    </span>
                @elseif(!$isSaleOpen)
                    <span class="flex items-center space-x-1 text-sm text-gray-500">
                        <i class="fas fa-lock text-xs"></i>
                        <span>Penjualan tidak tersedia</span>
                    </span>
                @else
                    <span class="flex items-center space-x-1 text-sm text-yellow-600">
                        <i class="fas fa-circle text-xs"></i>
                        <span>Tiket habis</span>
                    </span>
                @endif
            </div>
        </div>

        <div class="flex flex-col md:items-end justify-between">
            <div class="text-right">
                <span class="text-lg font-bold {{ $isSaleOpen ? 'text-gray-900' : 'text-gray-500' }}">
                    Rp{{ number_format($ticket->price, 0, ',', '.') }}
                </span>
            </div>

            <div class="mt-3">
                @if($ticket->quota_avail > 0 && $isSaleOpen)
                    <a href="{{ route('guest.orders.create', $ticket) }}" class="inline-block rounded-full bg-[#7B0015] px-5 py-2.5 font-bold text-white transition duration-300 hover:bg-[#E15B3F]">
                        Beli Tiket
                    </a>
                @elseif(!$isSaleOpen)
                    <button disabled class="inline-block py-2 px-4 bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed">
                        Penjualan Tidak Tersedia
                    </button>
                @else
                    <button disabled class="inline-block py-2 px-4 bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed">
                        Tiket Habis
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
