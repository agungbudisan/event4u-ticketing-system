@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Detail Acara: {{ $event->title }}</h1>
    <div class="flex space-x-2">
        <a href="{{ route('admin.tickets.index', $event) }}" class="inline-flex items-center rounded-full bg-[#7B0015] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-[#E15B3F]">
            <i class="fas fa-ticket-alt mr-2"></i> Kelola Tiket
        </a>
        <a href="{{ route('admin.events.edit', $event) }}" class="inline-flex items-center rounded-full bg-[#D1A83A] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-[#211F1C] transition hover:bg-[#E5BE52]">
            <i class="fas fa-edit mr-2"></i> Edit
        </a>
        <a href="{{ route('admin.events.index') }}" class="inline-flex items-center rounded-full bg-[#F1ECE3] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-[#211F1C] transition hover:bg-[#E9E1D5]">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>
</div>

<!-- Alert Success/Error -->
@if(session('success'))
    <div class="mb-4 rounded-xl border-l-4 border-green-600 bg-green-50 p-4 text-green-800 dark:bg-green-800/20 dark:text-green-400" role="alert">
        <p>{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 rounded-xl border-l-4 border-red-600 bg-red-50 p-4 text-red-800 dark:bg-red-800/20 dark:text-red-400" role="alert">
        <p>{{ session('error') }}</p>
    </div>
@endif

<div class="overflow-hidden rounded-2xl border border-[#D9DEE3] bg-white shadow-sm dark:bg-gray-800">
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Thumbnail dan Info Dasar -->
            <div class="md:col-span-1">
                @if($event->thumbnail)
                    <img src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}" class="w-full h-auto rounded-lg shadow">
                @else
                    <div class="flex h-48 w-full items-center justify-center rounded-xl bg-[#EEF1F4] shadow-sm dark:bg-gray-700">
                        <i class="fas fa-image text-4xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                @endif

                <div class="mt-4 rounded-xl bg-[#F8FAFB] p-4">
                    <h3 class="font-semibold text-lg mb-2 text-gray-900 dark:text-white">Info Acara</h3>

                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-tag mr-2 mt-1 h-5 w-5 text-[#7B0015]"></i>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Kategori</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $event->category->name ?? 'Tidak Ada Kategori' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt mr-2 mt-1 h-5 w-5 text-[#7B0015]"></i>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Lokasi</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $event->location }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <i class="fas fa-calendar-alt mr-2 mt-1 h-5 w-5 text-[#7B0015]"></i>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Acara</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $event->start_event->format('d M Y, H:i') }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">s/d {{ $event->end_event->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <i class="fas fa-ticket-alt mr-2 mt-1 h-5 w-5 text-[#7B0015]"></i>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Penjualan Tiket</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $event->start_sale->format('d M Y, H:i') }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">s/d {{ $event->end_sale->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        @if(isset($event->admin))
                        <div class="flex items-start">
                            <i class="fas fa-user mr-2 mt-1 h-5 w-5 text-[#7B0015]"></i>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Dibuat Oleh</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $event->admin->name ?? 'Admin' }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="flex items-start">
                            <i class="fas fa-clock mr-2 mt-1 h-5 w-5 text-[#7B0015]"></i>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Terakhir Diperbarui</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $event->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QR Code untuk Tiket (Opsional) -->
                {{-- <div class="mt-4 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h3 class="font-semibold text-lg mb-2 text-gray-900 dark:text-white">QR Acara</h3>
                    <div class="flex justify-center">
                        <div class="p-2 bg-white rounded-lg">
                            {!! QrCode::size(150)->generate(route('events.show', $event->id)) !!}
                        </div>
                    </div>
                    <p class="mt-2 text-center text-xs text-gray-500 dark:text-gray-400">QR Code untuk akses cepat ke halaman acara</p>
                </div> --}}
            </div>

            <!-- Deskripsi dan Detail Lainnya -->
            <div class="md:col-span-2">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">{{ $event->title }}</h2>

                <!-- Status Acara -->
                @php
                    $now = now();
                    $status = 'past';
                    $statusClass = 'bg-[#E2E8F0] text-[#334155]';
                    $statusText = 'Selesai';

                    if ($event->start_event > $now) {
                        $status = 'upcoming';
                        $statusClass = 'bg-[#DBEAFE] text-[#1E3A8A]';
                        $statusText = 'Mendatang';
                    } elseif ($event->end_event > $now) {
                        $status = 'ongoing';
                        $statusClass = 'bg-[#DCFCE7] text-[#166534]';
                        $statusText = 'Berlangsung';
                    }

                    // Status penjualan
                    $saleStatus = 'Penjualan ditutup';
                    $saleClass = 'bg-[#FEE2E2] text-[#991B1B]';

                    if ($now < $event->start_sale) {
                        $saleStatus = 'Penjualan belum dibuka';
                        $saleClass = 'bg-[#FEF3C7] text-[#92400E]';
                    } elseif ($now >= $event->start_sale && $now <= $event->end_sale) {
                        $saleStatus = 'Penjualan dibuka';
                        $saleClass = 'bg-[#DCFCE7] text-[#166534]';
                    }
                @endphp
                <div class="mb-4 flex flex-wrap gap-2">
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $statusClass }}">
                        {{ $statusText }}
                    </span>
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $saleClass }}">
                        {{ $saleStatus }}
                    </span>
                </div>

                <!-- Deskripsi -->
                <div class="mb-6 rounded-xl bg-[#F8FAFB] p-4">
                    <h3 class="font-semibold text-lg mb-2 text-gray-900 dark:text-white">Deskripsi</h3>
                    <div class="prose max-w-none text-gray-700 dark:text-gray-300">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                </div>

                <!-- Stage Layout -->
                @if($event->stage_layout)
                <div class="mb-6 rounded-xl bg-[#F8FAFB] p-4">
                    <h3 class="font-semibold text-lg mb-2 text-gray-900 dark:text-white">Layout Panggung</h3>
                    <div class="flex flex-col items-center">
                        <img src="{{ asset('storage/' . $event->stage_layout) }}" alt="Layout Panggung" class="max-h-96 w-auto rounded-lg shadow">
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Layout panggung acara akan ditampilkan kepada pembeli tiket</p>
                    </div>
                </div>
                @endif

                <!-- Tiket -->
                <div class="mb-6 rounded-xl bg-[#F8FAFB] p-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-lg text-gray-900 dark:text-white">Tiket Tersedia</h3>
                        <a href="{{ route('admin.tickets.create', $event) }}" class="inline-flex items-center rounded-full bg-[#7B0015] px-4 py-2 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-[#E15B3F] focus:outline-none">
                            <i class="fas fa-plus mr-1"></i> Tambah Tiket
                        </a>
                    </div>

                    @if($event->tickets->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-[#EEF1F4] dark:bg-gray-800">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis Tiket</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deskripsi</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Harga</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kuota</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach($event->tickets as $ticket)
                                    @php
                                        $sold = $ticket->orders->sum('quantity');
                                        $percentage = $ticket->quota_avail > 0 ? ($sold / $ticket->quota_avail) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $ticket->ticket_class }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($ticket->description, 50) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">Rp {{ number_format($ticket->price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">{{ $ticket->quota_avail }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $sold }}/{{ $ticket->quota_avail }} terjual</span>
                                                <div class="mt-1 h-1.5 w-full rounded-full bg-[#D9DEE3] dark:bg-gray-600">
                                                    <div class="h-1.5 rounded-full bg-[#E15B3F]" style="width: {{ min($percentage, 100) }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('admin.tickets.edit', $ticket) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus tiket ini? Semua pesanan terkait tiket ini akan terhapus.')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-4 text-gray-500 dark:text-gray-400">
                            <p>Belum ada tiket tersedia untuk acara ini.</p>
                            <p class="mt-2 text-sm">Klik tombol "Tambah Tiket" untuk membuat tiket baru.</p>
                        </div>
                    @endif
                </div>

                <!-- Statistik Penjualan -->
                @if($event->tickets->count() > 0)
                <div class="mb-6 rounded-xl bg-[#F8FAFB] p-4">
                    <h3 class="font-semibold text-lg mb-4 text-gray-900 dark:text-white">Statistik Penjualan Cepat</h3>
                    @php
                        $totalSales = 0;
                        $totalTickets = 0;
                        $totalSold = 0;

                        foreach($event->tickets as $ticket) {
                            $sold = $ticket->orders->sum('quantity');
                            $totalSales += $ticket->price * $sold;
                            $totalTickets += $ticket->quota_avail;
                            $totalSold += $sold;
                        }

                        $percentageSold = $totalTickets > 0 ? ($totalSold / $totalTickets) * 100 : 0;
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="rounded-xl border border-[#D9DEE3] bg-white p-3 shadow-sm dark:bg-gray-800">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Pendapatan</p>
                            <p class="text-xl font-semibold text-gray-900 dark:text-white">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-xl border border-[#D9DEE3] bg-white p-3 shadow-sm dark:bg-gray-800">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tiket Terjual</p>
                            <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $totalSold }} / {{ $totalTickets }}</p>
                            <div class="mt-2 h-1.5 w-full rounded-full bg-[#D9DEE3] dark:bg-gray-600">
                                <div class="h-1.5 rounded-full bg-[#E15B3F]" style="width: {{ min($percentageSold, 100) }}%"></div>
                            </div>
                        </div>
                        <div class="rounded-xl border border-[#D9DEE3] bg-white p-3 shadow-sm dark:bg-gray-800">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Persentase Terjual</p>
                            <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($percentageSold, 1) }}%</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dari total {{ $totalTickets }} tiket</p>
                        </div>
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('admin.analytics', $event->id) }}" class="inline-flex items-center rounded-full bg-[#17212B] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-[#7B0015]">
                                <i class="fas fa-chart-line mr-2"></i> Lihat Analitik
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
