<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} - Event4U</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Menghilangkan panah atas/bawah pada input number */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Untuk Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans @auth user-authenticated @endauth">

    @include('components.navbar')

    <main class="container mx-auto mb-16 mt-8 px-4 lg:px-6">
        <div class="relative rounded-xl overflow-hidden shadow-xl mb-6 bg-gradient-to-r from-[#7B0015] to-[#AF0020]">
            <!-- Header section with controlled height -->
            <div class="flex flex-col md:flex-row items-center">
                <!-- Thumbnail dengan ukuran terkontrol di sisi kiri (hanya pada desktop) -->
                @if($event->thumbnail)
                <div class="hidden md:block md:w-1/3 h-60 overflow-hidden">
                    <div class="w-full h-full relative">
                        <img src="{{ asset('storage/' . $event->thumbnail) }}"
                            alt="{{ $event->title }}"
                            class="object-contain w-full h-full p-2 cursor-pointer"
                            onclick="openFullImage('{{ asset('storage/' . $event->thumbnail) }}')" />
                        <button type="button"
                            onclick="openFullImage('{{ asset('storage/' . $event->thumbnail) }}')"
                            class="absolute bottom-2 right-2 bg-black/50 hover:bg-black/70 text-white text-xs px-2 py-1 rounded cursor-pointer transition">
                            <i class="fas fa-expand-alt"></i> Lihat
                        </button>
                    </div>
                </div>
                @endif

                <!-- Content di sisi kanan (atau penuh pada mobile) -->
                <div class="p-6 md:p-8 {{ $event->thumbnail ? 'md:w-2/3' : 'w-full' }}">
                    <div>
                        <div class="flex items-start justify-between mb-4">
                            <span class="inline-block bg-white/20 text-white text-xs px-2 py-1 rounded-full">
                                {{ $event->category->name ?? 'Event' }}
                            </span>

                            @php
                                $now = now();
                                $isSaleOpen = $now >= $event->start_sale && $now <= $event->end_sale;
                                $isUpcoming = $now < $event->start_event;
                                $isOngoing = $now >= $event->start_event && $now <= $event->end_event;
                                $isPast = $now > $event->end_event;
                            @endphp

                            <span class="inline-block
                                {{ $isUpcoming ? 'bg-blue-600' : ($isOngoing ? 'bg-green-600' : 'bg-gray-600') }}
                                text-white text-xs px-2 py-1 rounded">
                                {{ $isUpcoming ? 'Akan Datang' : ($isOngoing ? 'Sedang Berlangsung' : 'Selesai') }}
                            </span>
                        </div>

                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">{{ $event->title }}</h1>

                        <div class="flex flex-wrap text-white/80 text-sm gap-4 mt-4">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>{{ date('d F Y', strtotime($event->start_event)) }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock mr-2"></i>
                                <span>{{ date('H:i', strtotime($event->start_event)) }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span>{{ $event->location }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile thumbnail yang lebih kecil (hanya tampil di mobile) -->
            @if($event->thumbnail)
            <div class="md:hidden w-full h-48 bg-gray-100 overflow-hidden relative">
                <img src="{{ asset('storage/' . $event->thumbnail) }}"
                    alt="{{ $event->title }}"
                    class="object-contain w-full h-full cursor-pointer"
                    onclick="openFullImage('{{ asset('storage/' . $event->thumbnail) }}')" />
                <button type="button"
                        onclick="openFullImage('{{ asset('storage/' . $event->thumbnail) }}')"
                        class="absolute bottom-2 right-2 bg-black/50 hover:bg-black/70 text-white text-xs px-2 py-1 rounded cursor-pointer transition">
                    <i class="fas fa-expand-alt"></i> Lihat
                </button>
            </div>
            @endif
        </div>

        <section class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column - Event Details -->
            <div class="md:col-span-1 space-y-4">
                <!-- Date and Location Card -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-[#7B0015] text-white p-4">
                        <h2 class="font-bold text-lg">Informasi Event</h2>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-red-100 p-2 rounded-lg text-[#7B0015] flex items-center justify-center mr-3">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tanggal & Waktu</p>
                                <p class="font-medium">{{ date('d F Y', strtotime($event->start_event)) }}</p>
                                <p class="text-sm">{{ date('H:i', strtotime($event->start_event)) }} - {{ date('H:i', strtotime($event->end_event)) }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-red-100 p-2 rounded-lg text-[#7B0015] flex items-center justify-center mr-3">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Lokasi</p>
                                <p class="font-medium">{{ $event->location }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-red-100 p-2 rounded-lg text-[#7B0015] flex items-center justify-center mr-3">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Penjualan Tiket</p>
                                <p class="font-medium">
                                    {{ date('d F Y', strtotime($event->start_sale)) }} - {{ date('d F Y', strtotime($event->end_sale)) }}
                                </p>
                                @if($isSaleOpen)
                                    <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mt-1">
                                        Penjualan Dibuka
                                    </span>
                                @elseif($now < $event->start_sale)
                                    <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded mt-1">
                                        Penjualan Belum Dibuka
                                    </span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded mt-1">
                                        Penjualan Ditutup
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stage Layout -->
                @if($event->stage_layout && $event->has_stage_layout)
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-[#7B0015] text-white p-4">
                        <h2 class="font-bold text-lg">Layout Venue</h2>
                    </div>
                    <div class="p-4 relative">
                        <img src="{{ asset('storage/' . $event->stage_layout) }}"
                            alt="Layout Venue"
                            class="w-full h-auto rounded-lg cursor-pointer"
                            onclick="openFullImage('{{ asset('storage/' . $event->stage_layout) }}')" />
                        <button type="button"
                                onclick="openFullImage('{{ asset('storage/' . $event->stage_layout) }}')"
                                class="absolute bottom-2 right-2 bg-black/50 hover:bg-black/70 text-white text-xs px-2 py-1 rounded cursor-pointer transition">
                            <i class="fas fa-expand-alt"></i> Lihat
                        </button>
                    </div>
                </div>
                @endif

                <!-- Information Box -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-[#7B0015] text-white p-4">
                        <h2 class="font-bold text-lg">Informasi Pemesanan</h2>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex items-start text-sm text-gray-600">
                            <div class="text-blue-600 mr-2 mt-1">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <p class="flex-1 text-justify">
                                Maksimum 5 tiket per jenis tiket dalam sekali pemesanan.
                            </p>
                        </div>
                        <div class="flex items-start text-sm text-gray-600">
                            <div class="text-blue-600 mr-2 mt-1">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <p class="flex-1 text-justify">
                                Pembayaran harus dilakukan dalam waktu 1 jam setelah pemesanan.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Share Event -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-[#7B0015] text-white p-4">
                        <h2 class="font-bold text-lg">Bagikan Event</h2>
                    </div>
                    <div class="p-4 flex justify-center space-x-4">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            <i class="fab fa-facebook-square text-2xl"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($event->title) }}" target="_blank" class="text-blue-400 hover:text-blue-600">
                            <i class="fab fa-twitter-square text-2xl"></i>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($event->title . ' - ' . url()->current()) }}" target="_blank" class="text-green-600 hover:text-green-800">
                            <i class="fab fa-whatsapp-square text-2xl"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column - Description and Tickets -->
            <div class="md:col-span-2 space-y-6">
                <!-- Description -->
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="text-2xl font-bold mb-4">Deskripsi Event</h2>
                    <div class="prose prose-red max-w-none text-gray-700">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                </div>

                <!-- Tickets Section -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold">Tiket</h2>

                    @if(!$isSaleOpen)
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        @if($now < $event->start_sale)
                                            Penjualan tiket belum dimulai. Penjualan akan dibuka pada {{ date('d F Y, H:i', strtotime($event->start_sale)) }}.
                                        @else
                                            Penjualan tiket sudah ditutup.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div x-data="{ selectedTicket: null }">
                        @foreach($event->tickets as $ticket)
                            <div class="bg-white border-2 {{ $isSaleOpen ? 'border-red-700' : 'border-gray-300' }} rounded-xl overflow-hidden shadow-md mb-4">
                                <div class="p-6 grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-4">
                                    <div>
                                        <h3 class="text-xl font-bold {{ $isSaleOpen ? 'text-[#7B0015]' : 'text-gray-600' }}">{{ $ticket->ticket_class }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $ticket->description ?? 'Tiket ' . $ticket->ticket_class }}</p>
                                        <div class="mt-2 flex items-center">
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

                                        @if($ticket->quota_avail > 0 && $isSaleOpen)
                                            <button type="button"
                                                @click="selectedTicket = selectedTicket === {{ $ticket->id }} ? null : {{ $ticket->id }}"
                                                class="mt-2 md:mt-0 py-2 px-4 rounded-lg"
                                                :class="selectedTicket === {{ $ticket->id }} ? 'bg-[#950019] text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-800'">
                                                <span x-text="selectedTicket === {{ $ticket->id }} ? 'Batalkan' : 'Pilih Tiket'"></span>
                                            </button>
                                        @elseif(!$isSaleOpen)
                                            <button disabled class="mt-2 md:mt-0 py-2 px-4 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed">
                                                Penjualan Tidak Tersedia
                                            </button>
                                        @else
                                            <button disabled class="mt-2 md:mt-0 py-2 px-4 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed">
                                                Tiket Habis
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Quantity Selection (only shows when ticket is selected) -->
                                <div x-show="selectedTicket === {{ $ticket->id }}" x-transition class="bg-gray-50 border-t border-gray-200 p-4">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">Jumlah Tiket:</span>

                                        <div class="flex items-center">
                                            <button type="button"
                                                onclick="decrease('{{ $ticket->id }}')"
                                                class="w-10 h-10 flex items-center justify-center bg-gray-200 text-gray-700 rounded-l hover:bg-gray-300">
                                                <i class="fas fa-minus"></i>
                                            </button>

                                            <input type="number"
                                                id="qty-{{ $ticket->id }}"
                                                data-ticket-id="{{ $ticket->id }}"
                                                data-name="{{ $ticket->ticket_class }}"
                                                data-price="{{ $ticket->price }}"
                                                data-stock="{{ $ticket->quota_avail }}"
                                                min="1"
                                                max="{{ min(5, $ticket->quota_avail) }}"
                                                value="1"
                                                oninput="validateInput(this)"
                                                class="ticket-qty w-16 h-10 text-center outline-none border border-gray-200 px-1">

                                            <button type="button"
                                                onclick="increase('{{ $ticket->id }}')"
                                                class="w-10 h-10 flex items-center justify-center bg-gray-200 text-gray-700 rounded-r hover:bg-gray-300">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex justify-end">
                                        <button type="button"
                                            onclick="addToSummary('{{ $ticket->id }}')"
                                            class="py-2 px-4 bg-[#7B0015] hover:bg-[#950019] text-white font-medium rounded-lg">
                                            Tambahkan ke Pesanan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Order Summary -->
                        <div class="bg-white p-6 rounded-xl shadow-md space-y-4 sticky bottom-0 md:relative border-t-2 border-[#7B0015]">
                            <h2 class="text-xl font-bold mb-2">Ringkasan Pesanan</h2>
                            <ul id="order-summary" class="text-gray-800 text-sm space-y-2">
                                <li class="text-gray-500">Belum ada tiket dipilih.</li>
                            </ul>
                            <div class="pt-4 border-t border-gray-200">
                                <p class="font-semibold text-lg flex justify-between">
                                    <span>Total:</span>
                                    <span id="total-price" class="text-[#7B0015]">Rp0</span>
                                </p>

                                <!-- Form untuk pemilihan tiket dengan data-attribute route -->
                                <form action="{{ route('orders.create', ['ticket' => 0]) }}" method="GET" id="order-form"
                                    data-base-route="{{ route('orders.create', ':ticket_id') }}"
                                    data-guest-route="{{ route('guest.orders.create', ':ticket_id') }}"
                                    onsubmit="return prepareOrderData()">
                                    <input type="hidden" name="qty" id="qty-param">
                                    <button type="submit"
                                        class="w-full mt-4 bg-[#7B0015] hover:bg-[#950019] text-white font-bold py-3 px-6 rounded-lg transition-all transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#7B0015] disabled:hover:scale-100"
                                        {{ !$isSaleOpen ? 'disabled' : '' }} id="order-button" disabled>
                                        {{ $isSaleOpen ? 'Pesan Tiket' : 'Penjualan Tidak Tersedia' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('components.footer')

    <script>
        function openFullImage(src) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/90';
            modal.style.backdropFilter = 'blur(5px)';
            modal.innerHTML = `
                <div class="relative max-w-4xl max-h-[90vh] p-4">
                    <img src="${src}" alt="Full Image" class="max-w-full max-h-[80vh] object-contain">
                    <button type="button" class="absolute top-2 right-2 bg-white text-black p-2 rounded-full hover:bg-gray-200" id="close-modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(modal);

            // Prevent scrolling when modal is open
            document.body.style.overflow = 'hidden';

            // Handling close button
            const closeButton = modal.querySelector('#close-modal');
            closeButton.addEventListener('click', function() {
                modal.remove();
                document.body.style.overflow = '';
            });

            // Tutup modal saat click di luar gambar
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                    document.body.style.overflow = '';
                }
            });

            // Close on escape key
            const escHandler = function(e) {
                if (e.key === 'Escape') {
                    modal.remove();
                    document.body.style.overflow = '';
                    document.removeEventListener('keydown', escHandler);
                }
            };
            document.addEventListener('keydown', escHandler);
        }

        // Definisikan selectedTicket sebagai variabel global
        let selectedTicket = null;

        function addToSummary(ticketId) {
            const input = document.getElementById('qty-' + ticketId);
            if (!input) {
                console.error('Input element not found for ticket ID:', ticketId);
                return;
            }

            const qty = parseInt(input.value || 1);
            const name = input.dataset.name;
            const price = parseInt(input.dataset.price);

            console.log('Adding to summary:', { ticketId, qty, name, price });

            // Simpan data tiket yang dipilih
            selectedTicket = {
                id: ticketId,
                name: name,
                price: price,
                quantity: qty
            };

            // Update ringkasan pesanan
            updateSummary();

            // Enable tombol pesan
            const orderButton = document.getElementById('order-button');
            if (orderButton) {
                orderButton.disabled = false;
            }
        }

        function increase(id) {
            const input = document.getElementById('qty-' + id);
            if (!input) return;

            const max = Math.min(5, parseInt(input.dataset.stock || 5));
            let val = parseInt(input.value || 1);

            if (val < max) {
                input.value = val + 1;
            }

            console.log('Increased quantity for ticket:', id, 'New value:', input.value);
        }

        function decrease(id) {
            const input = document.getElementById('qty-' + id);
            if (!input) return;

            let val = parseInt(input.value || 1);

            if (val > 1) {
                input.value = val - 1;
            }

            console.log('Decreased quantity for ticket:', id, 'New value:', input.value);
        }

        function validateInput(input) {
            if (!input) return;

            // Memastikan hanya angka positif dan maksimum 5
            let val = parseInt(input.value) || 1;
            const max = Math.min(5, parseInt(input.dataset.stock || 5));

            // Batasi nilai antara 1 dan max
            val = Math.max(1, Math.min(val, max));

            // Update nilai
            input.value = val;
        }

        function updateSummary() {
            const summary = document.getElementById('order-summary');
            const totalPriceEl = document.getElementById('total-price');
            const orderButton = document.getElementById('order-button');

            if (!summary || !totalPriceEl || !orderButton) {
                console.error('One or more summary elements not found');
                return;
            }

            // Jika tidak ada tiket yang dipilih
            if (!selectedTicket) {
                summary.innerHTML = '<li class="text-gray-500">Belum ada tiket dipilih.</li>';
                totalPriceEl.textContent = 'Rp0';
                orderButton.disabled = true;
                return;
            }

            const subtotal = selectedTicket.price * selectedTicket.quantity;

            summary.innerHTML = `
                <li class="flex justify-between items-center py-2">
                    <span>${selectedTicket.name} × ${selectedTicket.quantity}</span>
                    <span>Rp${subtotal.toLocaleString('id-ID')}</span>
                </li>
            `;
            totalPriceEl.textContent = 'Rp' + subtotal.toLocaleString('id-ID');
            orderButton.disabled = false;

            console.log('Summary updated with ticket:', selectedTicket);
        }

        function prepareOrderData() {
            if (!selectedTicket) {
                alert('Silakan pilih tiket terlebih dahulu.');
                return false;
            }

            // Dapatkan form element
            const form = document.getElementById('order-form');
            if (!form) {
                console.error('Form tidak ditemukan');
                return false;
            }

            // Buat URL dengan parameter qty
            let baseUrl;

            @auth
                baseUrl = "{{ route('orders.create', ':ticket_id') }}".replace(':ticket_id', selectedTicket.id);
            @else
                baseUrl = "{{ route('guest.orders.create', ':ticket_id') }}".replace(':ticket_id', selectedTicket.id);
            @endauth

            // Set form action dengan URL dasar + parameter qty
            form.action = baseUrl;

            // Tambahkan input hidden untuk qty
            let qtyInput = document.getElementById('qty-param');
            if (!qtyInput) {
                qtyInput = document.createElement('input');
                qtyInput.type = 'hidden';
                qtyInput.id = 'qty-param';
                qtyInput.name = 'qty';
                form.appendChild(qtyInput);
            }
            qtyInput.value = selectedTicket.quantity;

            // Hapus input tickets jika ada
            const ticketsInput = document.getElementById('tickets-data');
            if (ticketsInput) {
                ticketsInput.parentNode.removeChild(ticketsInput);
            }

            console.log('Form disiapkan dengan URL:', form.action, 'dan qty:', selectedTicket.quantity);
            return true;
        }

        // Initialize when document is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Set max values for all ticket inputs
            document.querySelectorAll('.ticket-qty').forEach(input => {
                const max = Math.min(5, parseInt(input.dataset.stock));
                input.setAttribute('max', max);
            });

            // Disable tombol pesan secara default
            document.getElementById('order-button').disabled = true;
        });
    </script>
</body>
</html>
