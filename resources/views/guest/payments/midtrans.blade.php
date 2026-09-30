<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pembayaran Midtrans - {{ $order->ticket->event->title }} - Event4U</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
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
                <span class="text-gray-700">Pembayaran</span>
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

            <!-- Header Pembayaran -->
            <div class="bg-gradient-to-r from-[#7B0015] to-[#AF0020] rounded-t-xl p-6 text-white">
                <h1 class="text-2xl font-bold mb-1">Pembayaran</h1>
                <p>{{ $order->ticket->event->title }}</p>
                <p class="text-sm mt-2">Nomor Pesanan: {{ $order->reference }}</p>
            </div>

            <!-- Form Pembayaran -->
            <div class="bg-white rounded-b-xl shadow-md p-6">
                <!-- Countdown Timer -->
                <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-md"
                     x-data="countdown({{ $expires_at ?? ($order->expires_at ? $order->expires_at->timestamp * 1000 : now()->addHour()->timestamp * 1000) }})">
                    <div class="flex items-start">
                        <div class="text-yellow-500 mr-2 mt-1">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-yellow-700">Selesaikan pembayaran Anda dalam:</p>
                            <p class="text-lg font-bold" x-text="formattedTime"></p>
                        </div>
                    </div>
                </div>

                <!-- Info Waktu Pembayaran -->
                <div class="mb-6 p-4 bg-gray-50 border-l-4 border-gray-300 rounded-md">
                    <div class="flex items-start">
                        <div class="text-gray-500 mr-2 mt-1">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700">Informasi Waktu Pembayaran:</p>
                            <ul class="text-sm text-gray-600 mt-2 ml-4 list-disc">
                                <li>Transfer Bank: 24 jam</li>
                                <li>QRIS: 15 menit</li>
                                <li>GoPay/ShopeePay: 15 menit</li>
                                <li>Kartu Kredit: Langsung diproses</li>
                                <li>Indomaret/Alfamart: 24 jam</li>
                            </ul>
                            <p class="text-sm mt-2">Timer akan diperbarui setelah Anda memilih metode pembayaran.</p>
                        </div>
                    </div>
                </div>

                <!-- Guest Information Summary -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h2 class="font-semibold mb-3">Informasi Pengguna</h2>
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
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h2 class="font-semibold mb-4">Ringkasan Pesanan</h2>
                    <div class="mb-3">
                        <p class="font-medium">{{ $order->ticket->event->title }}</p>
                        <p class="text-sm text-gray-600">{{ $order->ticket->event->start_event->format('d F Y, H:i') }}</p>
                        <p class="text-sm text-gray-600">{{ $order->ticket->event->location }}</p>
                    </div>
                    <div class="mb-3">
                        <div class="flex justify-between text-sm py-2 border-b border-gray-200">
                            <span>{{ $order->ticket->ticket_class }}</span>
                            <span>{{ $order->quantity }} × Rp {{ number_format($order->ticket->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold py-2">
                            <span>Total</span>
                            <span class="text-[#7B0015]">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Instructions (muncul setelah memilih metode pembayaran) -->
                <div id="payment-instructions" class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-md hidden">
                    <div class="flex items-start">
                        <div class="text-blue-500 mr-2 mt-1">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-blue-700">Instruksi Pembayaran:</p>
                            <div id="instruction-content" class="text-sm text-blue-700 mt-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Information -->
                <div class="mb-6">
                    <div class="p-4 border border-[#7B0015] bg-red-50 rounded-md">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-credit-card text-[#7B0015]"></i>
                            </div>
                            <div>
                                <p class="font-semibold">Midtrans Payment Gateway</p>
                                <p class="text-sm text-gray-600">Kartu Kredit, Bank Transfer, E-Wallet, QRIS, dll</p>
                            </div>
                        </div>
                        <div class="mt-4 ml-12 text-sm">
                            <p>Klik tombol "Bayar Sekarang" untuk memulai proses pembayaran dengan Midtrans.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button id="pay-button" class="w-full py-3 bg-[#7B0015] hover:bg-[#950019] text-white font-medium rounded-lg shadow-md transition duration-300">
                        Bayar Sekarang
                    </button>
                </div>
            </div>
        </div>
    </main>

    @include('components.footer')

    <script>
        // AlpineJS component untuk countdown
        function countdown(serverExpiryTime) {
            return {
                expiryTime: serverExpiryTime,
                formattedTime: '00:00:00',
                interval: null,
                orderKey: 'guest_payment_expiry_{{ $order->reference }}',

                calculateTimeLeft() {
                    const now = new Date().getTime();
                    const distance = this.expiryTime - now;

                    if (distance <= 0) {
                        clearInterval(this.interval);
                        this.formattedTime = '00:00:00';
                        localStorage.removeItem(this.orderKey);
                        // Redirect to expired page
                        window.location.href = "{{ route('events.show', $order->ticket->event) }}";
                        return;
                    }

                    // Calculate time units
                    const hours = Math.floor(distance / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    // Format time
                    this.formattedTime =
                        hours.toString().padStart(2, '0') + ':' +
                        minutes.toString().padStart(2, '0') + ':' +
                        seconds.toString().padStart(2, '0');
                },

                init() {
                    // Check if we have a stored expiry time
                    const storedExpiry = localStorage.getItem(this.orderKey);

                    if (storedExpiry) {
                        // Use the stored expiry time if it's in the future
                        const storedExpiryTime = parseInt(storedExpiry);
                        if (storedExpiryTime > new Date().getTime()) {
                            this.expiryTime = storedExpiryTime;
                        } else {
                            // If stored time is in the past, use server time and update storage
                            localStorage.setItem(this.orderKey, this.expiryTime);
                        }
                    } else {
                        // Store the expiry time from server
                        localStorage.setItem(this.orderKey, this.expiryTime);
                    }

                    // Listen for expiry time updates
                    window.addEventListener('updateExpiryTime', (event) => {
                        this.expiryTime = event.detail.expiryTime;
                        localStorage.setItem(this.orderKey, this.expiryTime);
                        console.log('Expiry time updated to:', new Date(this.expiryTime));
                    });

                    // Calculate immediately
                    this.calculateTimeLeft();

                    // Update every second
                    this.interval = setInterval(() => {
                        this.calculateTimeLeft();
                    }, 1000);
                }
            };
        }

        let expiryTimeUpdated = false;

        // Function untuk memeriksa status order secara berkala
        function checkOrderStatus() {
            const reference = "{{ $order->reference }}";
            const checkStatusUrl = "{{ route('guest.payments.check-status', $order->reference) }}";

            // Periksa setiap 5 detik
            const statusInterval = setInterval(() => {
                fetch(checkStatusUrl)
                    .then(response => response.json())
                    .then(data => {
                        // Jika ada pembaruan waktu kedaluwarsa (setelah pemilihan metode pembayaran)
                        if (data.expires_at && !expiryTimeUpdated) {
                            // Perbarui waktu kedaluwarsa untuk countdown timer
                            window.dispatchEvent(new CustomEvent('updateExpiryTime', {
                                detail: { expiryTime: data.expires_at }
                            }));
                            expiryTimeUpdated = true;
                        }

                        // Tampilkan instruksi pembayaran jika ada
                        if (data.instructions && data.payment_method) {
                            displayPaymentInstructions(data.payment_method, data.instructions);
                        }

                        if (data.status === 'completed') {
                            clearInterval(statusInterval);
                            window.location.href = "{{ route('guest.orders.confirmation', $order->reference) }}?status=success";
                        } else if (['expired', 'failed', 'cancelled'].includes(data.status)) {
                            clearInterval(statusInterval);
                            window.location.href = "{{ route('guest.orders.confirmation', $order->reference) }}?status=failed";
                        }
                        // Untuk status 'pending', terus periksa
                    })
                    .catch(error => console.error('Error checking order status:', error));
            }, 5000);
        }

        // Function untuk menampilkan instruksi pembayaran
        function displayPaymentInstructions(paymentMethod, instructions) {
            const instructionsDiv = document.getElementById('payment-instructions');
            const instructionContent = document.getElementById('instruction-content');

            if (!instructions || instructions.length === 0) {
                instructionsDiv.classList.add('hidden');
                return;
            }

            let content = '';

            // Format instruksi sesuai metode pembayaran
            if (paymentMethod === 'bank_transfer') {
                content += '<p class="font-medium mt-2">Transfer Bank:</p>';
                instructions.forEach(inst => {
                    content += `<p class="mt-1">Bank: ${inst.bank}</p>`;
                    if (inst.va_number) {
                        content += `<p>Nomor VA: <span class="font-bold">${inst.va_number}</span></p>`;
                    }
                });
            } else if (paymentMethod === 'cstore') {
                content += '<p class="font-medium mt-2">Pembayaran di Toko:</p>';
                instructions.forEach(inst => {
                    content += `<p class="mt-1">${inst.store}: <span class="font-bold">${inst.payment_code}</span></p>`;
                });
            } else if (paymentMethod === 'qris') {
                content += '<p class="font-medium mt-2">QRIS:</p>';
                content += '<p>Silakan scan QR code melalui aplikasi yang mendukung pembayaran QRIS.</p>';
            } else {
                content += '<p class="mt-1">Silakan ikuti petunjuk pembayaran yang muncul pada layar Midtrans.</p>';
            }

            instructionContent.innerHTML = content;
            instructionsDiv.classList.remove('hidden');
        }

        // Midtrans Snap Integration
        document.addEventListener('DOMContentLoaded', function() {
            const payButton = document.getElementById('pay-button');
            const snapToken = "{{ $snap_token }}";

            payButton.addEventListener('click', function() {
                snap.pay(snapToken, {
                    onSuccess: function(result) {
                        window.location.href = "{{ route('guest.payments.midtrans.finish', $order->reference) }}?transaction_status=settlement&order_id=" + result.order_id + "&result=" + encodeURIComponent(JSON.stringify(result));
                    },
                    onPending: function(result) {
                        window.location.href = "{{ route('guest.payments.midtrans.finish', $order->reference) }}?transaction_status=pending&order_id=" + result.order_id + "&result=" + encodeURIComponent(JSON.stringify(result));
                    },
                    onError: function(result) {
                        window.location.href = "{{ route('guest.payments.midtrans.finish', $order->reference) }}?transaction_status=failed&order_id=" + result.order_id + "&result=" + encodeURIComponent(JSON.stringify(result));
                    },
                    onClose: function() {
                        alert('Anda menutup popup tanpa menyelesaikan pembayaran. Silakan coba lagi dengan klik tombol Bayar Sekarang.');
                    }
                });
            });

            // Mulai cek status order
            checkOrderStatus();
        });
    </script>
</body>
</html>
