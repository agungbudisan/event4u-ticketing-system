@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Kelola Pesanan</h1>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Daftar semua pesanan dari user.</p>
</div>

<!-- Filter & Search -->
<div class="mb-6 overflow-hidden rounded-2xl border border-[#D9DEE3] bg-white shadow-sm dark:bg-gray-800">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Filter & Pencarian</h3>
    </div>
    <div class="p-6">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Search Field -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-[#F8FAFB] shadow-sm focus:border-[#E15B3F] focus:ring-[#E15B3F]"
                        placeholder="Cari berdasarkan ID, nama pembeli, email..."
                    >
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Pembayaran</label>
                    <select
                        name="status"
                        id="status"
                        class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-[#F8FAFB] shadow-sm focus:border-[#E15B3F] focus:ring-[#E15B3F]"
                    >
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Pembayaran Berhasil</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Pembayaran Dibatalkan</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Pembayaran Gagal</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Pembayaran Kedaluwarsa</option>
                    </select>
                </div>

                <!-- Event Filter -->
                <div>
                    <label for="event" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Acara</label>
                    <select
                        name="event_id"
                        id="event"
                        class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-[#F8FAFB] shadow-sm focus:border-[#E15B3F] focus:ring-[#E15B3F]"
                    >
                        <option value="">Semua Acara</option>
                        @php
                            $events = \App\Models\Event::orderBy('title')->get();
                        @endphp

                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dari Tanggal</label>
                    <input
                        type="date"
                        name="date_from"
                        id="date_from"
                        value="{{ request('date_from') }}"
                        class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-[#F8FAFB] shadow-sm focus:border-[#E15B3F] focus:ring-[#E15B3F]"
                    >
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
                    <input
                        type="date"
                        name="date_to"
                        id="date_to"
                        value="{{ request('date_to') }}"
                        class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-[#F8FAFB] shadow-sm focus:border-[#E15B3F] focus:ring-[#E15B3F]"
                    >
                </div>

                <!-- Sort By -->
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Urutkan Berdasarkan</label>
                    <select
                        name="sort"
                        id="sort"
                        class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-[#F8FAFB] shadow-sm focus:border-[#E15B3F] focus:ring-[#E15B3F]"
                    >
                        <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('admin.orders.index') }}" class="mr-3 inline-flex items-center rounded-full border border-[#D9DEE3] bg-white px-5 py-2.5 text-sm font-bold text-[#52606D] hover:bg-[#EEF1F4] focus:outline-none">
                    <i class="fas fa-times mr-2"></i>
                    Reset
                </a>
                <button type="submit" class="inline-flex items-center rounded-full border border-transparent bg-[#17212B] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#7B0015] focus:outline-none">
                    <i class="fas fa-search mr-2"></i>
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="overflow-hidden rounded-2xl border border-[#D9DEE3] bg-white shadow-sm dark:bg-gray-800">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Daftar Pesanan</h3>
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Total: {{ $orders->total() }} pesanan
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-[#EEF1F4] dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        ID/Referensi
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Acara
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Pembeli
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Tanggal
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Jumlah Tiket
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Total Harga
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($orders as $order)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $order->reference ?? '#'.str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $order->ticket->event->title ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $order->user->name ?? $order->guest_name ?? 'N/A' }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $order->email }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $order->order_date->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $order->quantity }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
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
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        <div class="py-8">
                            <i class="fas fa-search text-4xl mb-3 text-gray-400 dark:text-gray-600"></i>
                            <p class="text-lg font-medium">Tidak ada pesanan yang ditemukan</p>
                            <p class="text-sm mt-1">Coba ubah filter pencarian</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Order Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
    <!-- Total Orders -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center">
                <i class="fas fa-shopping-cart text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pesanan</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $orders->total() }}</p>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pendapatan</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    Rp {{ number_format($totalRevenue ?? $orders->sum('total_price'), 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Completed Orders -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pesanan Selesai</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $completedOrders ?? $orders->filter(function($order) {
                        return isset($order->payment) && $order->payment->status == 'completed';
                    })->count() }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $orders->withQueryString()->links() }}
</div>
@endsection
