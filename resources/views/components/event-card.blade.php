<a href="{{ route('events.show', $event->id) }}" class="group block overflow-hidden rounded-2xl border border-[#E9E1D5] bg-white shadow-[0_8px_30px_rgba(33,31,28,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_16px_40px_rgba(33,31,28,0.12)]">
    <div class="relative overflow-hidden">
        @if($event->thumbnail)
            <img src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}"
                class="h-52 w-full object-cover transition-transform duration-500 group-hover:scale-105">
        @else
            <div class="flex h-52 w-full items-center justify-center bg-[#F1ECE3]">
                <i class="fas fa-calendar-alt text-gray-400 text-4xl"></i>
            </div>
        @endif

        <!-- Status Badge -->
        @php
            $now = now();
            $isUpcoming = $now < $event->start_event;
            $isOngoing = $now >= $event->start_event && $now <= $event->end_event;
        @endphp
        <div class="absolute right-4 top-4">
            <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold backdrop-blur-sm
                {{ $isUpcoming ? 'bg-white/90 text-[#7B0015]' : ($isOngoing ? 'bg-[#E15B3F] text-white' : 'bg-[#211F1C]/80 text-white') }}">
                {{ $isUpcoming ? 'Upcoming' : ($isOngoing ? 'Ongoing' : 'Past') }}
            </span>
        </div>

        <!-- Category Badge -->
        <div class="absolute left-4 top-4">
            <span class="inline-block rounded-full bg-[#F8F4EC]/90 px-3 py-1 text-xs font-semibold text-[#7B0015] backdrop-blur-sm">
                {{ $event->category->name ?? 'Event' }}
            </span>
        </div>
    </div>

    <div class="p-5">
        <h3 class="mb-3 line-clamp-2 text-xl font-bold transition group-hover:text-[#7B0015]">{{ $event->title }}</h3>

        <div class="mb-3 flex flex-wrap gap-3 text-sm text-gray-500">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt mr-2 text-[#7B0015]"></i>
                <span>{{ date('d M Y', strtotime($event->start_event)) }}</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-map-marker-alt mr-2 text-[#7B0015]"></i>
                <span class="truncate">{{ $event->location }}</span>
            </div>
        </div>

        @php
            $minPrice = $event->tickets->min('price') ?? 0;
            $maxPrice = $event->tickets->max('price') ?? 0;
            $isSaleOpen = $now >= $event->start_sale && $now <= $event->end_sale;
            $isEndedSale = $now > $event->end_sale;

            // Format harga dengan format mata uang Rupiah yang benar
            $formattedMinPrice = 'Rp' . number_format($minPrice, 2, ',', '.');
            $formattedMaxPrice = 'Rp' . number_format($maxPrice, 2, ',', '.');
        @endphp

        <div class="mt-5 flex items-center justify-between border-t border-[#E9E1D5] pt-4">
            <!-- Price range dengan format mata uang Rupiah yang benar -->
            <p class="font-bold text-[#7B0015]">
                @if($minPrice == $maxPrice)
                    {{ $formattedMinPrice }}
                @else
                    {{ $formattedMinPrice }} - {{ $formattedMaxPrice }}
                @endif
            </p>

            <!-- Ticket status indicator -->
            <span class="text-sm {{ $isSaleOpen ? 'text-green-600' : ($isEndedSale ? 'text-red-600' : 'text-gray-500') }}">
                <i class="fas {{ $isSaleOpen ? 'fa-ticket-alt' : ($isEndedSale ? 'fa-times-circle' : 'fa-clock') }} mr-1"></i>
                {{ $isSaleOpen ? 'Available' : ($now < $event->start_sale ? 'Soon' : 'Ended') }}
            </span>
        </div>
    </div>
</a>
