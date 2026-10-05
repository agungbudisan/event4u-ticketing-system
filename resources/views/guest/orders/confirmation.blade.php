<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan - {{ $order->ticket->event->title }} - Event4U</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#F8F4EC] text-[#211F1C] font-sans">

    @include('components.navbar')

    <main class="container mx-auto mb-16 mt-8 px-4 lg:px-6">
        <div class="max-w-3xl mx-auto">
            <!-- Breadcrumb -->
            <nav class="flex items-center text-sm text-gray-500 mb-6">
                <a href="{{ route('welcome') }}" class="hover:text-[#7B0015]">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ route('events.index') }}" class="hover:text-[#7B0015]">Events</a>
                <span class="mx-2">/</span>
                <a href="{{ route('events.show', $order->ticket->event) }}" class="hover:text-[#7B0015]">{{ $order->ticket->event->title }}</a>
                <span class="mx-2">/</span>
                <span class="text-gray-700">Konfirmasi</span>
            </nav>

            @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-md">
                {{ session('error') }}
            </div>
            @endif

            <!-- Success Card -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-check text-green-600 text-2xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Terima Kasih!</h1>
                    <p class="text-gray-600 mb-4">Pesanan Anda telah berhasil diproses.</p>

                    <div class="flex justify-center items-center space-x-2 mb-6">
                        <span class="text-gray-600">Nomor Pesanan:</span>
                        <span class="font-medium text-[#7B0015]">{{ $order->reference }}</span>
                    </div>

                    <div class="w-full max-w-md bg-gray-50 p-4 rounded-lg mb-4">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600">Status Pembayaran:</span>
                            <span class="px-2 py-1 text-xs rounded-full {{ $order->payment->status === 'completed' ? 'bg-green-100 text-green-800' : ($order->payment->status === 'expired' || $order->isExpired() ? 'bg-gray-100 text-gray-800' : ($order->payment->status === 'failed' || $order->payment->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                {{ $order->isExpired() && $order->payment->status !== 'completed' ? 'Pembayaran Kedaluwarsa' : $order->payment->status_label }}
                            </span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600">Metode Pembayaran:</span>
                            <span>
                                @if($order->payment->method === 'transfer')
                                    Transfer Bank
                                @elseif($order->payment->method === 'ewallet')
                                    E-Wallet
                                @elseif($order->payment->method === 'credit_card')
                                    Kartu Kredit
                                @else
                                    {{ $order->payment->method }}
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal Pembayaran:</span>
                            <span>{{ $order->payment->payment_date->format('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    @if($order->payment->status === 'completed')
                        <p class="text-sm text-gray-500 mb-6">
                            E-ticket telah dikirim ke email Anda: <span class="font-medium">{{ $order->email }}</span>
                        </p>
                    @else
                        <p class="text-sm text-gray-500 mb-6">
                            E-ticket akan dikirim ke email Anda: <span class="font-medium">{{ $order->email }}</span> setelah pembayaran dikonfirmasi oleh admin.
                        </p>
                    @endif

                    <div class="flex flex-col sm:flex-row gap-3 w-full max-w-md">
                        <a href="{{ route('events.index') }}" class="flex-1 py-2 px-4 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg text-center transition duration-300">
                            Lihat Events Lainnya
                        </a>
                        @if($order->payment->status === 'completed')
                        <button type="button" onclick="showETicket()" class="flex-1 py-2 px-4 bg-[#7B0015] hover:bg-[#950019] text-white font-medium rounded-lg text-center transition duration-300">
                            Lihat E-Ticket
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- E-Ticket Card (hanya tampil jika pembayaran sudah dikonfirmasi) -->
            @if($order->payment->status === 'completed')
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6" id="e-ticket" style="display: none;">
                <div class="bg-gradient-to-r from-[#7B0015] to-[#AF0020] p-4 text-white">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">E-Ticket</h2>
                        <span class="text-sm">{{ $order->reference }}</span>
                    </div>
                </div>

                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">{{ $order->ticket->event->title }}</h3>
                            <p class="text-gray-600">{{ $order->ticket->event->start_event->format('l, d F Y') }}</p>
                            <p class="text-gray-600">{{ $order->ticket->event->start_event->format('H:i') }} WIB</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">TIPE TIKET</p>
                            <p class="font-bold">{{ $order->ticket->ticket_class }}</p>
                        </div>
                    </div>

                    <!-- Ticket Details -->
                    <div class="mb-6">
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-600">Jumlah Tiket</span>
                            <span class="font-medium">{{ $order->quantity }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-600">Nama</span>
                            <span class="font-medium">{{ $order->guest_name }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-600">Email</span>
                            <span class="font-medium">{{ $order->email }}</span>
                        </div>
                    </div>

                    <!-- Venue Information -->
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2">Lokasi Event</h4>
                        <p class="text-gray-800">{{ $order->ticket->event->location }}</p>
                    </div>

                    <!-- QR Code -->
                    <div class="flex justify-center mb-6">
                        <div class="p-4 bg-gray-100 rounded">
                            {!! QrCode::size(180)->generate(
                                json_encode([
                                    'reference' => $order->reference,
                                    'event' => $order->ticket->event->title,
                                    'ticket_class' => $order->ticket->ticket_class,
                                    'quantity' => $order->quantity,
                                    'attendee' => $order->guest_name,
                                    'email' => $order->email
                                ])
                            ) !!}
                            <p class="text-xs text-center text-gray-600 mt-2">SCAN ME</p>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="text-sm text-gray-600 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-start mb-2">
                            <i class="fas fa-info-circle text-[#7B0015] mt-1 mr-2"></i>
                            <p>Tunjukkan e-ticket ini (baik dari email atau halaman ini) saat memasuki venue.</p>
                        </div>
                        <div class="flex items-start mb-2">
                            <i class="fas fa-clock text-[#7B0015] mt-1 mr-2"></i>
                            <p>Mohon hadir 30 menit sebelum acara dimulai untuk proses registrasi.</p>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-[#7B0015] mt-1 mr-2"></i>
                            <p>E-ticket ini tidak dapat dipindahtangankan dan hanya berlaku untuk {{ $order->quantity }} {{ $order->quantity > 1 ? 'orang' : 'orang' }}.</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 flex justify-center">
                    <a href="{{ route('guest.orders.download-ticket', $order->reference) }}" class="py-2 px-4 bg-gray-800 hover:bg-black text-white font-medium rounded-lg text-center transition duration-300 flex items-center">
                        <i class="fas fa-download mr-2"></i> Unduh E-Ticket
                    </a>
                </div>
            </div>
            @endif

            <!-- Order Details -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-[#7B0015] to-[#AF0020] p-4 text-white">
                    <h2 class="text-xl font-bold">Detail Pesanan</h2>
                </div>

                <div class="p-6 space-y-6">
                    <div>
                        <h3 class="font-semibold mb-2 text-lg">Informasi Event</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="font-medium">{{ $order->ticket->event->title }}</p>
                            <div class="flex flex-wrap gap-4 mt-2 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt text-[#7B0015] mr-2"></i>
                                    <span>{{ $order->ticket->event->start_event->format('d F Y') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-[#7B0015] mr-2"></i>
                                    <span>{{ $order->ticket->event->start_event->format('H:i') }} WIB</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt text-[#7B0015] mr-2"></i>
                                    <span>{{ $order->ticket->event->location }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-2 text-lg">Informasi Tiket</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex justify-between mb-1">
                                <span>Jenis Tiket:</span>
                                <span class="font-medium">{{ $order->ticket->ticket_class }}</span>
                            </div>
                            <div class="flex justify-between mb-1">
                                <span>Harga per Tiket:</span>
                                <span>Rp {{ number_format($order->ticket->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between mb-1">
                                <span>Jumlah:</span>
                                <span>{{ $order->quantity }}</span>
                            </div>
                            <div class="flex justify-between font-bold pt-2 border-t border-gray-200 mt-2">
                                <span>Total:</span>
                                <span class="text-[#7B0015]">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-2 text-lg">Informasi Pemesan</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-600">Nama:</p>
                                    <p class="font-medium">{{ $order->guest_name }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Email:</p>
                                    <p class="font-medium">{{ $order->email }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">No. Telepon:</p>
                                    <p class="font-medium">{{ $order->guest_phone }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Tanggal Order:</p>
                                    <p class="font-medium">{{ $order->order_date->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-2 text-lg">Customer Support</h3>
                        <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                            <p class="text-sm">Jika Anda memiliki pertanyaan atau membutuhkan bantuan, silakan hubungi:</p>
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-[#7B0015] mr-2"></i>
                                <span>support@event4u.com</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-phone text-[#7B0015] mr-2"></i>
                                <span>+62 812 3456 7890</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('components.footer')

    <script>
        function showETicket() {
            const eTicket = document.getElementById('e-ticket');
            if (eTicket.style.display === 'none') {
                eTicket.style.display = 'block';
                // Scroll to the e-ticket
                eTicket.scrollIntoView({ behavior: 'smooth' });
            } else {
                eTicket.style.display = 'none';
            }
        }

        function printETicket() {
            const printContents = document.getElementById('e-ticket').innerHTML;
            const originalContents = document.body.innerHTML;

            document.body.innerHTML = `
                <div style="padding: 20px;">
                    ${printContents}
                </div>
            `;

            window.print();
            document.body.innerHTML = originalContents;
            // Reinitialize the event handlers
            document.getElementById('e-ticket').style.display = 'block';
        }
    </script>
</body>
</html>
