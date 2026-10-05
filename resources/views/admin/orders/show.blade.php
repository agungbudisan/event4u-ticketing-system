@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
        Detail Pesanan: {{ $order->reference ?? '#'.str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
    </h1>
    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
        <i class="fas fa-arrow-left mr-2"></i> Kembali
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Order Summary -->
            <div class="md:col-span-1">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ringkasan Pesanan</h2>

                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">ID/Referensi Pesanan</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $order->reference ?? '#'.str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Pesanan</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                @if(is_string($order->order_date))
                                {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y, H:i') }}
                                @else
                                    {{ $order->order_date->format('d M Y, H:i') }}
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Status Pembayaran</p>
                            @php
                                $statusClass = 'bg-[#E2E8F0] text-[#334155]';
                                $statusText = $order->payment ? $order->payment->status_label : 'Belum Dibayar';

                                if(isset($order->payment)) {
                                    if($order->payment->status == 'pending') {
                                        $statusClass = 'bg-[#FEF3C7] text-[#92400E]';
                                    } elseif($order->payment->status == 'completed') {
                                        $statusClass = 'bg-[#DCFCE7] text-[#166534]';
                                    } elseif($order->payment->status == 'cancelled') {
                                        $statusClass = 'bg-[#FEE2E2] text-[#991B1B]';
                                    } elseif($order->payment->status == 'failed') {
                                        $statusClass = 'bg-[#FEE2E2] text-[#991B1B]';
                                    } elseif($order->payment->status == 'expired') {
                                        $statusClass = 'bg-[#E2E8F0] text-[#334155]';
                                    }
                                }
                            @endphp
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </div>

                        @if(isset($order->payment))
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Metode Pembayaran</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $order->payment->method }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Pembayaran</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                @if(is_string($order->order_date))
                                {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y, H:i') }}
                                @else
                                    {{ $order->order_date->format('d M Y, H:i') }}
                                @endif
                            </p>
                        </div>
                        @endif

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Jumlah Tiket</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $order->quantity }} tiket</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Harga</p>
                            <p class="text-lg font-semibold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    @if(isset($order->payment) && $order->payment->status == 'pending')
                    <div class="mt-6">
                        <form action="{{ route('admin.payments.updateStatus', $order->payment) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ubah Status Pembayaran</label>
                            <div class="flex space-x-2">
                                <select name="status" id="status" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm">
                                    <option value="pending" {{ $order->payment->status == 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                                    <option value="completed" {{ $order->payment->status == 'completed' ? 'selected' : '' }}>Pembayaran Berhasil</option>
                                    <option value="cancelled" {{ $order->payment->status == 'cancelled' ? 'selected' : '' }}>Pembayaran Dibatalkan</option>
                                    <option value="failed" {{ $order->payment->status == 'failed' ? 'selected' : '' }}>Pembayaran Gagal</option>
                                    <option value="expired" {{ $order->payment->status == 'expired' ? 'selected' : '' }}>Pembayaran Kedaluwarsa</option>
                                </select>
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Order Details -->
            <div class="md:col-span-2">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Acara</h2>
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <div class="flex flex-col md:flex-row">
                            <div class="md:w-1/3">
                                <img src="{{ asset('storage/' . $order->ticket->event->thumbnail) }}" alt="{{ $order->ticket->event->title }}" class="h-48 w-full object-cover">
                            </div>
                            <div class="p-4 md:w-2/3">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $order->ticket->event->title }}</h3>
                                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-map-marker-alt mr-2 text-indigo-500 dark:text-indigo-400"></i>
                                        <span>{{ $order->ticket->event->location }}</span>
                                    </div>
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-calendar-alt mr-2 text-indigo-500 dark:text-indigo-400"></i>
                                        <span>{{ $order->ticket->event->start_event->format('d M Y, H:i') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-ticket-alt mr-2 text-indigo-500 dark:text-indigo-400"></i>
                                        <span>{{ $order->ticket->name ?? $order->ticket->ticket_class }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Pembeli</h2>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tipe Pengguna</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    @if($order->uid)
                                        <span class="inline-flex items-center">
                                            <i class="fas fa-user-check text-green-500 mr-1"></i> Pengguna Terdaftar
                                        </span>
                                    @else
                                        <span class="inline-flex items-center">
                                            <i class="fas fa-user text-gray-500 mr-1"></i> Tamu
                                        </span>
                                    @endif
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $order->email }}</p>
                            </div>

                            @if($order->uid && isset($order->user))
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nama</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $order->user->name }}</p>
                            </div>
                            @elseif($order->guest_name)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nama</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $order->guest_name }}</p>
                            </div>
                            @endif

                            @if($order->guest_phone)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Telepon</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $order->guest_phone }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Payment History (if applicable) -->
                @if(isset($order->payment))
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Riwayat Pembayaran</h2>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Metode</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        @if(is_string($order->order_date))
                                        {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y, H:i') }}
                                        @else
                                            {{ $order->order_date->format('d M Y, H:i') }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $order->payment->method }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $paymentStatusClass = 'bg-[#E2E8F0] text-[#334155]';

                                            if($order->payment->status == 'pending') {
                                                $paymentStatusClass = 'bg-[#FEF3C7] text-[#92400E]';
                                            } elseif($order->payment->status == 'completed') {
                                                $paymentStatusClass = 'bg-[#DCFCE7] text-[#166534]';
                                            } elseif($order->payment->status == 'cancelled') {
                                                $paymentStatusClass = 'bg-[#FEE2E2] text-[#991B1B]';
                                            } elseif($order->payment->status == 'failed') {
                                                $paymentStatusClass = 'bg-[#FEE2E2] text-[#991B1B]';
                                            } elseif($order->payment->status == 'expired') {
                                                $paymentStatusClass = 'bg-[#E2E8F0] text-[#334155]';
                                            }
                                        @endphp
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $paymentStatusClass }}">
                                            {{ $order->payment->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-right">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
