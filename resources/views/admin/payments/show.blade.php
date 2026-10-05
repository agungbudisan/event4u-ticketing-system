@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
        Detail Pembayaran #{{ $payment->id }}
    </h1>
    <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
        <i class="fas fa-arrow-left mr-2"></i> Kembali
    </a>
</div>

<!-- Card Informasi Pembayaran -->
<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-6">
    <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-money-bill-wave mr-2 text-indigo-600 dark:text-indigo-400"></i>
            Informasi Pembayaran
        </h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID Pembayaran</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Metode Pembayaran</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
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
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1">
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
                            <span class="px-2 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                <i class="fas {{ $statusIcon }} mr-1"></i>
                                {{ $payment->status_label }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pembayaran</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $payment->created_at->format('d M Y, H:i') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Terakhir Diperbarui</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $payment->updated_at->format('d M Y, H:i') }}
                        </dd>
                    </div>
                </dl>
            </div>
            <div>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID Pesanan</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            <a href="{{ route('admin.orders.show', $payment->order) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                {{ $payment->order->reference ?? '#'.str_pad($payment->order->id, 5, '0', STR_PAD_LEFT) }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                            Rp {{ number_format($payment->order->total_price, 0, ',', '.') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Catatan Pembeli</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $payment->notes ?? '-' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Info Pembeli -->
<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-6">
    <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-user mr-2 text-indigo-600 dark:text-indigo-400"></i>
            Informasi Pembeli
        </h2>
    </div>
    <div class="p-6">
        @if($payment->order->user)
        <!-- User terdaftar -->
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($payment->order->user->name) }}" alt="{{ $payment->order->user->name }}" class="h-10 w-10 rounded-full">
            </div>
            <div class="ml-4">
                <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $payment->order->user->name }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $payment->order->email }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    <i class="fas fa-user-tag mr-1"></i> User Terdaftar
                </p>
            </div>
        </div>
        @else
        <!-- Guest user -->
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                    <i class="fas fa-user text-gray-500 dark:text-gray-400"></i>
                </div>
            </div>
            <div class="ml-4">
                <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $payment->order->guest_name }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $payment->order->email }}</p>
                @if($payment->order->guest_phone)
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $payment->order->guest_phone }}</p>
                @endif
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    <i class="fas fa-user-clock mr-1"></i> Pembeli Tamu
                </p>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Detail Acara dan Tiket -->
<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-6">
    <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-ticket-alt mr-2 text-indigo-600 dark:text-indigo-400"></i>
            Detail Acara dan Tiket
        </h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <!-- Event Image -->
            <div class="md:col-span-1">
                @if($payment->order->ticket->event->thumbnail)
                    <img src="{{ asset('storage/' . $payment->order->ticket->event->thumbnail) }}" alt="{{ $payment->order->ticket->event->title }}" class="w-full h-auto rounded-lg">
                @else
                    <div class="w-full h-32 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-gray-400 dark:text-gray-500 text-3xl"></i>
                    </div>
                @endif
            </div>

            <!-- Event and Ticket Details -->
            <div class="md:col-span-4">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ $payment->order->ticket->event->title }}
                </h3>
                <div class="mt-2 mb-4 flex flex-wrap gap-x-4 gap-y-2 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-day mr-2 text-indigo-600 dark:text-indigo-400"></i>
                        {{ $payment->order->ticket->event->start_event->format('d M Y') }}
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock mr-2 text-indigo-600 dark:text-indigo-400"></i>
                        {{ $payment->order->ticket->event->start_event->format('H:i') }} WIB
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt mr-2 text-indigo-600 dark:text-indigo-400"></i>
                        {{ $payment->order->ticket->event->location }}
                    </div>
                </div>

                <!-- Ticket Info -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex justify-between mb-2">
                        <span class="font-medium text-gray-900 dark:text-white">{{ $payment->order->ticket->ticket_class }}</span>
                        <span class="text-gray-900 dark:text-white">{{ $payment->order->quantity }} x Rp {{ number_format($payment->order->ticket->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-semibold">
                        <span class="text-gray-900 dark:text-white">Total</span>
                        <span class="text-indigo-600 dark:text-indigo-400">Rp {{ number_format($payment->order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-cogs mr-2 text-indigo-600 dark:text-indigo-400"></i>
            Aksi
        </h2>
    </div>
    <div class="p-6">
        <form id="statusForm" action="{{ route('admin.payments.updateStatus', $payment) }}" method="POST">
            @csrf
            @method('PUT')

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Update Status Pembayaran</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="pending" {{ $payment->status === 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="completed" {{ $payment->status === 'completed' ? 'selected' : '' }}>Pembayaran Berhasil</option>
                    <option value="cancelled" {{ $payment->status === 'cancelled' ? 'selected' : '' }}>Pembayaran Dibatalkan</option>
                    <option value="failed" {{ $payment->status === 'failed' ? 'selected' : '' }}>Pembayaran Gagal</option>
                    <option value="expired" {{ $payment->status === 'expired' ? 'selected' : '' }}>Pembayaran Kedaluwarsa</option>
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    <i class="fas fa-info-circle mr-1"></i> Jika status diubah menjadi "Selesai", tiket akan otomatis dikirim ke email pembeli.
                </p>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-save mr-2"></i> Update Status
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
