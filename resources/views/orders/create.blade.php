<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tiket - {{ $ticket->event->title }} - Event4U</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                <a href="{{ route('events.show', $ticket->event) }}" class="hover:text-[#7B0015]">{{ $ticket->event->title }}</a>
                <span class="mx-2">/</span>
                <span class="text-gray-700">Order</span>
            </nav>

            <!-- Header Order -->
            <div class="bg-gradient-to-r from-[#7B0015] to-[#AF0020] rounded-t-xl p-6 text-white">
                <h1 class="text-2xl font-bold mb-1">Pemesanan Tiket</h1>
                <p>{{ $ticket->event->title }}</p>
            </div>

            <!-- Form Pemesanan -->
            <div class="bg-white rounded-b-xl shadow-md p-6" x-data="{
                quantity: {{ $quantity ?? 1 }},
                price: {{ $ticket->price }},
                get total() {
                    return this.quantity * this.price;
                }
            }">
                <!-- Ticket Info -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border-l-4 border-[#7B0015]">
                    <div class="flex items-start">
                        <div class="flex-1">
                            <h2 class="text-xl font-semibold text-[#7B0015]">{{ $ticket->ticket_class }}</h2>
                            <p class="text-gray-600 mt-1">{{ $ticket->description }}</p>

                            <div class="mt-3 flex flex-wrap gap-4 text-sm">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt text-[#7B0015] mr-2"></i>
                                    <span>{{ $ticket->event->start_event->format('d F Y') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-[#7B0015] mr-2"></i>
                                    <span>{{ $ticket->event->start_event->format('H:i') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt text-[#7B0015] mr-2"></i>
                                    <span>{{ $ticket->event->location }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-[#7B0015]">Rp {{ number_format($ticket->price, 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-500">Tersedia: {{ $ticket->quota_avail }} tiket</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('orders.store', $ticket) }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Data Pemesan</h3>

                        <!-- Data pengguna yang sudah login -->
                        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-[#7B0015] text-white rounded-full flex items-center justify-center">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600">Anda login sebagai</p>
                                    <p class="font-medium">{{ Auth::user()->name }}</p>
                                    <p class="text-sm">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-medium mb-2">Alamat Email Konfirmasi</label>
                            <input type="email" name="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B0015]" value="{{ Auth::user()->email }}" required>
                            <p class="text-sm text-gray-500 mt-1">E-ticket akan dikirim ke email ini</p>
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Jumlah Tiket</h3>

                        <div class="flex items-center">
                            <button type="button" x-on:click="quantity = Math.max(1, quantity - 1)" class="w-10 h-10 flex items-center justify-center bg-gray-200 text-gray-700 rounded-l hover:bg-gray-300">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" name="quantity" id="quantity" x-model="quantity" min="1" max="{{ min(5, $ticket->quota_avail) }}" class="w-16 h-10 border-t border-b border-gray-300 text-center" required>
                            <button type="button" x-on:click="quantity = Math.min({{ min(5, $ticket->quota_avail) }}, quantity + 1)" class="w-10 h-10 flex items-center justify-center bg-gray-200 text-gray-700 rounded-r hover:bg-gray-300">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Maksimum 5 tiket per pemesanan</p>
                        @error('quantity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border-t-2 border-[#7B0015]">
                        <h3 class="font-semibold mb-3">Ringkasan Pesanan</h3>
                        <div class="flex justify-between mb-2">
                            <span>{{ $ticket->ticket_class }}</span>
                            <span x-text="'× ' + quantity"></span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span>Harga per tiket:</span>
                            <span>Rp {{ number_format($ticket->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-3 border-t border-gray-200 flex justify-between font-bold">
                            <span>Total:</span>
                            <span class="text-[#7B0015]" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(total)"></span>
                        </div>
                    </div>

                    <div class="bg-gray-100 p-4 rounded-lg mb-6">
                        <div class="flex items-start">
                            <div class="text-[#7B0015] mr-2 mt-1">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <p class="text-sm text-gray-600 flex-1">
                                Dengan melanjutkan, Anda menyetujui syarat dan ketentuan yang berlaku. Pemesanan harus diselesaikan dalam waktu 1 jam.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="w-full py-3 bg-[#7B0015] hover:bg-[#950019] text-white font-medium rounded-lg shadow-md transition duration-300">
                            Lanjutkan ke Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    @include('components.footer')
</body>
</html>
