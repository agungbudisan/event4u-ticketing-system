@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Kelola Pembayaran</h1>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Daftar semua pembayaran dari pengguna.</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Total Payments -->
    <div class="rounded-2xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#7B0015]">
                <i class="fas fa-money-check-alt text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pembayaran</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $payments->total() }}</p>
            </div>
        </div>
    </div>

    <!-- Completed Payments -->
    <div class="rounded-2xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#2F855A]">
                <i class="fas fa-check-circle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pembayaran Berhasil</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalCompleted }}</p>
            </div>
        </div>
    </div>

    <!-- Pending Payments -->
    <div class="rounded-2xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#B7791F]">
                <i class="fas fa-clock text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Menunggu Pembayaran</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalPending }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search -->
<div class="mb-6 overflow-hidden rounded-2xl border border-[#D9DEE3] bg-white shadow-sm dark:bg-gray-800">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Filter & Pencarian</h3>
    </div>
    <div class="p-6">
        <form action="{{ route('admin.payments.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Search Field -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-[#F8FAFB] shadow-sm focus:border-[#E15B3F] focus:ring-2 focus:ring-[#E15B3F]/30"
                        placeholder="Cari berdasarkan ID, nama pembeli, email..."
                    >
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Pembayaran</label>
                    <select
                        name="status"
                        id="status"
                        class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-[#F8FAFB] shadow-sm focus:border-[#E15B3F] focus:ring-2 focus:ring-[#E15B3F]/30"
                    >
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Pembayaran Berhasil</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Pembayaran Dibatalkan</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Pembayaran Gagal</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Pembayaran Kedaluwarsa</option>
                    </select>
                </div>

                <!-- Payment Method Filter -->
                <div>
                    <label for="method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Metode Pembayaran</label>
                    <select
                        name="method"
                        id="method"
                        class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-[#F8FAFB] shadow-sm focus:border-[#E15B3F] focus:ring-2 focus:ring-[#E15B3F]/30"
                    >
                        <option value="">Semua Metode</option>
                        <option value="transfer" {{ request('method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="credit_card" {{ request('method') == 'credit_card' ? 'selected' : '' }}>Kartu Kredit</option>
                        <option value="ewallet" {{ request('method') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                    </select>
                </div>

                <!-- Event Filter -->
                <div>
                    <label for="event" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Acara</label>
                    <select
                        name="event_id"
                        id="event"
                        class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-[#F8FAFB] shadow-sm focus:border-[#E15B3F] focus:ring-2 focus:ring-[#E15B3F]/30"
                    >
                        <option value="">Semua Acara</option>
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
                        class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-[#F8FAFB] shadow-sm focus:border-[#E15B3F] focus:ring-2 focus:ring-[#E15B3F]/30"
                    >
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
                    <input
                        type="date"
                        name="date_to"
                        id="date_to"
                        value="{{ request('date_to') }}"
                        class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-[#F8FAFB] shadow-sm focus:border-[#E15B3F] focus:ring-2 focus:ring-[#E15B3F]/30"
                    >
                </div>

                <!-- Sort By -->
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Urutkan Berdasarkan</label>
                    <select
                        name="sort"
                        id="sort"
                        class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-[#F8FAFB] shadow-sm focus:border-[#E15B3F] focus:ring-2 focus:ring-[#E15B3F]/30"
                    >
                        <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="amount_high" {{ request('sort') == 'amount_high' ? 'selected' : '' }}>Nominal Tertinggi</option>
                        <option value="amount_low" {{ request('sort') == 'amount_low' ? 'selected' : '' }}>Nominal Terendah</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#E15B3F]/30 mr-3">
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

<!-- Payments Table -->
<div class="overflow-hidden rounded-2xl border border-[#D9DEE3] bg-white shadow-sm dark:bg-gray-800">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Daftar Pembayaran</h3>
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Total: {{ $payments->total() }} pembayaran
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
            <thead class="bg-[#EEF1F4] dark:bg-gray-700">
                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acara</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pembeli</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Metode</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($payments as $payment)
                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                    <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        #{{ $payment->id }}
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $payment->order->ticket->event->title ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $payment->order->user->name ?? $payment->order->guest_name ?? 'N/A' }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $payment->order->email }}
                        </div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        @if($payment->method === 'transfer')
                            <span class="inline-flex items-center">
                                <i class="fas fa-university mr-2"></i> Transfer Bank
                            </span>
                        @elseif($payment->method === 'credit_card')
                            <span class="inline-flex items-center">
                                <i class="fas fa-credit-card mr-2"></i> Kartu Kredit
                            </span>
                        @elseif($payment->method === 'ewallet')
                            <span class="inline-flex items-center">
                                <i class="fas fa-wallet mr-2"></i> E-Wallet
                            </span>
                        @else
                            {{ ucfirst($payment->method) }}
                        @endif
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        Rp {{ number_format($payment->order->total_price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $payment->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        @php
                            $statusClass = 'bg-[#E2E8F0] text-[#334155]';
                            $statusIcon = 'fa-question-circle';

                            if($payment->status === 'pending') {
                                $statusClass = 'bg-[#FEF3C7] text-[#92400E]';
                                $statusIcon = 'fa-clock';
                            } elseif($payment->status === 'completed') {
                                $statusClass = 'bg-[#DCFCE7] text-[#166534]';
                                $statusIcon = 'fa-check-circle';
                            } elseif($payment->status === 'cancelled') {
                                $statusClass = 'bg-[#FEE2E2] text-[#991B1B]';
                                $statusIcon = 'fa-times-circle';
                            } elseif($payment->status === 'failed') {
                                $statusClass = 'bg-[#FEE2E2] text-[#991B1B]';
                                $statusIcon = 'fa-exclamation-circle';
                            } elseif($payment->status === 'expired') {
                                $statusClass = 'bg-[#E2E8F0] text-[#334155]';
                                $statusIcon = 'fa-hourglass-end';
                            }
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                            <i class="fas {{ $statusIcon }} mr-1"></i>
                            {{ $payment->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.payments.show', $payment) }}" class="inline-flex items-center rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-semibold text-gray-700 hover:border-[#E15B3F] hover:text-[#7B0015] dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 transition">Detail</a>
                    </td>
                </tr>
                @empty
                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                    <td colspan="8" class="px-6 py-5 text-center text-sm text-gray-500 dark:text-gray-400">
                        <div class="py-8">
                            <i class="fas fa-money-check text-4xl mb-3 text-gray-400 dark:text-gray-600"></i>
                            <p class="text-lg font-medium">Tidak ada pembayaran yang ditemukan</p>
                            <p class="text-sm mt-1">Coba ubah filter pencarian</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $payments->withQueryString()->links() }}
</div>
@endsection
