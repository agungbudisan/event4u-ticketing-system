@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Kelola Tiket: {{ $event->title }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Anda dapat membuat, mengedit, dan menghapus tiket untuk acara ini</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('admin.tickets.create', $event) }}" class="inline-flex items-center rounded-full bg-[#7B0015] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-[#E15B3F]">
            <i class="fas fa-plus mr-2"></i> Tambah Tiket
        </a>
        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center rounded-full bg-[#F1ECE3] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-[#211F1C] transition hover:bg-[#E9E1D5]">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>
</div>

<!-- Event Info Card -->
<div class="mb-6 rounded-2xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
    <div class="flex flex-col md:flex-row md:items-center">
        <div class="flex-shrink-0 mb-4 md:mb-0 md:mr-4">
            <img src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}" class="w-32 h-32 object-cover rounded-lg">
        </div>
        <div class="flex-1">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $event->title }}</h2>
            <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Lokasi</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $event->location }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Acara</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $event->start_event->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Penjualan Tiket</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $event->start_sale->format('d M Y') }} - {{ $event->end_sale->format('d M Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ticket List -->
<div class="overflow-hidden rounded-2xl border border-[#D9DEE3] bg-white shadow-sm dark:bg-gray-800">
    <div class="p-4 border-b dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Tiket</h2>
    </div>

    @if($tickets->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-[#EEF1F4] dark:bg-gray-700">
                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis Tiket</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deskripsi</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Harga</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kuota</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($tickets as $ticket)
                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $ticket->ticket_class }}</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($ticket->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($ticket->price, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-right">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $ticket->quota_avail }}</div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-center">
                            @php
                                $now = now();
                                $isSaleActive = $event->start_sale <= $now && $event->end_sale >= $now;
                                $sold = $ticket->orders->sum('quantity') ?? 0;
                                $salePercentage = $ticket->quota_avail > 0 ? ($sold / $ticket->quota_avail) * 100 : 0;
                            @endphp

                            @if($isSaleActive)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Aktif</span>
                            @else
                                @if($event->end_sale < $now)
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Selesai</span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Belum Mulai</span>
                                @endif
                            @endif

                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5 mt-2">
                                <div class="h-1.5 rounded-full bg-[#E15B3F]" style="width: {{ $salePercentage }}%"></div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $sold }}/{{ $ticket->quota_avail }} ({{ number_format($salePercentage, 1) }}%)</div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.tickets.edit', $ticket) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus tiket ini?')">
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
        <div class="p-8 text-center">
            <div class="text-gray-500 dark:text-gray-400">
                <i class="fas fa-ticket-alt text-4xl mb-3"></i>
                <p class="text-lg">Belum ada tiket untuk acara ini</p>
                <p class="text-sm mt-2">Klik tombol Tambah Tiket untuk mulai membuat tiket baru.</p>
            </div>
        </div>
    @endif
</div>
@endsection
