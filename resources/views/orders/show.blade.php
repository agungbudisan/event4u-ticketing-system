<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Pesanan - {{ $order->ticket->event->title }} - Event4U</title>
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
                <span class="text-gray-700">Detail Pesanan</span>
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

            @if (session('info'))
            <div class="mb-6 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 rounded-md">
                {{ session('info') }}
            </div>
            @endif

            <!-- Success Card -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-check text-green-600 text-2xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Terima Kasih, {{ Auth::user()->name }}!</h1>
                    <p class="text-gray-600 mb-4">Pesanan Anda telah berhasil diproses.</p>

                    <div class="flex justify-center items-center space-x-2 mb-6">
                        <span class="text-gray-600">ID Pesanan:</span>
                        <span class="font-medium text-[#7B0015]">#{{ $order->id }}</span>
                    </div>

                    <div class="w-full max-w-md bg-gray-50 p-4 rounded-lg mb-4">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600">Status Pembayaran:</span>
                            @php
                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                $statusText = 'Menunggu Pembayaran';

                                if($order->payment) {
                                    if($order->payment->status === 'completed') {
                                        $statusClass = 'bg-green-100 text-green-800';
                                        $statusText = 'Sukses';
                                    } elseif($order->payment->status === 'pending') {
                                        if ($order->isExpired()) {
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                            $statusText = 'Kedaluwarsa';
                                        } else {
                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                            $statusText = 'Menunggu Konfirmasi';
                                        }
                                    } elseif($order->payment->status === 'expired') {
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        $statusText = 'Kedaluwarsa';
                                    } elseif($order->payment->status === 'failed') {
                                        $statusClass = 'bg-red-100 text-red-800';
                                        $statusText = 'Gagal';
                                    } elseif($order->payment->status === 'cancelled') {
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        $statusText = 'Dibatalkan';
                                    }
                                } else {
                                    if ($order->isExpired()) {
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        $statusText = 'Kedaluwarsa';
                                    } else {
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                        $statusText = 'Belum Dibayar';
                                    }
                                }
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </div>
                        @if($order->payment)
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
                        @endif
                    </div>

                    @if($order->payment && $order->payment->status === 'completed')
                    <p class="text-sm text-gray-500 mb-6">
                        E-ticket akan dikirim ke email Anda: <span class="font-medium">{{ $order->email }}</span>
                    </p>
                    @endif

                    <div class="flex flex-col sm:flex-row gap-3 w-full max-w-md">
                        <a href="{{ route('events.index') }}" class="flex-1 py-2 px-4 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg text-center transition duration-300">
                            Lihat Events Lainnya
                        </a>
                        <a href="{{ route('orders.index') }}" class="flex-1 py-2 px-4 bg-gray-800 hover:bg-black text-white font-medium rounded-lg text-center transition duration-300">
                            Lihat Pesanan Saya
                        </a>

                        @php
                            $paymentExpired = $order->isExpired() || ($order->payment && $order->payment->expires_at && now()->isAfter($order->payment->expires_at));
                        @endphp

                        @if($order->payment && $order->payment->status === 'completed')
                            <button type="button" onclick="showETicket()" class="flex-1 py-2 px-4 bg-[#7B0015] hover:bg-[#950019] text-white font-medium rounded-lg text-center transition duration-300">
                                Lihat E-Ticket
                            </button>
                        @elseif(!$paymentExpired && (!$order->payment || $order->payment->status === 'pending' || $order->payment->status === 'failed'))
                            <a href="{{ route('payments.midtrans', $order) }}" class="flex-1 py-2 px-4 bg-[#7B0015] hover:bg-[#950019] text-white font-medium rounded-lg text-center transition duration-300">
                                Bayar Sekarang
                            </a>
                        @elseif($paymentExpired && (!$order->payment || $order->payment->status !== 'completed'))
                            <span class="flex-1 py-2 px-4 bg-gray-200 text-gray-500 font-medium rounded-lg text-center transition duration-300">
                                Pembayaran Kedaluwarsa
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- E-Ticket Card -->
            @if($order->payment && $order->payment->status === 'completed')
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6" id="e-ticket" style="display: none;">
                <div class="bg-gradient-to-r from-[#7B0015] to-[#AF0020] p-4 text-white">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">E-Ticket</h2>
                        <span class="text-sm">#{{ $order->id }}</span>
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
                            <span class="font-medium">{{ Auth::user()->name }}</span>
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
                                    'order_id' => $order->id,
                                    'event' => $order->ticket->event->title,
                                    'ticket_class' => $order->ticket->ticket_class,
                                    'quantity' => $order->quantity,
                                    'attendee' => Auth::user()->name,
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
                    <a href="{{ route('orders.download-ticket', $order) }}" class="py-2 px-4 bg-gray-800 hover:bg-black text-white font-medium rounded-lg text-center transition duration-300 flex items-center">
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
                                    <p class="font-medium">{{ Auth::user()->name }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Email:</p>
                                    <p class="font-medium">{{ $order->email }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">ID Pengguna:</p>
                                    <p class="font-medium">{{ Auth::id() }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Tanggal Order:</p>
                                    <p class="font-medium">{{ $order->order_date->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Action Button for Pending/Failed Payments -->
                    @php
                        $orderTime = $order->order_date;
                        $now = now();
                        $diffInHours = $now->diffInHours($orderTime);
                        $paymentExpired = $diffInHours >= 1;
                    @endphp

                    @if(!$paymentExpired && (!$order->payment || $order->payment->status === 'pending' || $order->payment->status === 'failed'))
                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-yellow-500 mt-1"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">
                                    Pembayaran Belum Selesai
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Pesanan Anda belum dibayar atau pembayaran masih dalam proses. Silakan selesaikan pembayaran sebelum batas waktu berakhir.</p>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('payments.midtrans', $order) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest bg-[#7B0015] hover:bg-[#950019] focus:outline-none focus:border-[#950019] focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        Bayar Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @elseif($paymentExpired && (!$order->payment || $order->payment->status !== 'completed'))
                    <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-gray-500 mt-1"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-gray-800">
                                    Pembayaran Kedaluwarsa
                                </h3>
                                <div class="mt-2 text-sm text-gray-700">
                                    <p>Batas waktu pembayaran telah berakhir. Silakan buat pesanan baru jika Anda masih ingin membeli tiket untuk event ini.</p>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('events.show', $order->ticket->event) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50 focus:outline-none focus:border-gray-300 focus:ring ring-gray-200 disabled:opacity-25 transition ease-in-out duration-150">
                                        Lihat Detail Event
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

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
