@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Tambah Tiket Baru: {{ $event->title }}</h1>
    <a href="{{ route('admin.tickets.index', $event) }}" class="inline-flex items-center rounded-full bg-[#F1ECE3] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-[#211F1C] transition hover:bg-[#E9E1D5]">
        <i class="fas fa-arrow-left mr-2"></i> Kembali
    </a>
</div>

<!-- Event Info Card -->
<div class="mb-6 rounded-2xl border border-[#E9E1D5] bg-white p-4 shadow-sm dark:bg-gray-800">
    <div class="flex items-center">
        <div class="flex-shrink-0 mr-4">
            <img src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}" class="w-16 h-16 object-cover rounded-lg">
        </div>
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $event->title }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $event->start_event->format('d M Y, H:i') }} di {{ $event->location }}</p>
        </div>
    </div>
</div>

<div
    x-data="{
        ticketClass: '',
        price: 0,
        quota: 100,
        description: '',

        formattedPrice() {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(this.price);
        },

        totalRevenue() {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(this.price * this.quota);
        }
    }"
    class="overflow-hidden rounded-2xl border border-[#E9E1D5] bg-white shadow-sm dark:bg-gray-800"
>
    <div class="p-6">
        <form action="{{ route('admin.tickets.store', $event) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jenis Tiket -->
                <div>
                    <label for="ticket_class" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Tiket <span class="text-red-500">*</span></label>
                    <input
                        x-model="ticketClass"
                        type="text"
                        name="ticket_class"
                        id="ticket_class"
                        value="{{ old('ticket_class') }}"
                        class="mt-1 block w-full rounded-xl border-[#E9E1D5] bg-[#F8F4EC] shadow-sm focus:border-[#E15B3F] focus:ring-2 focus:ring-[#E15B3F]/30 sm:text-sm"
                        required
                    >
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Contoh: VIP, Regular, Early Bird, dll.</p>
                    @error('ticket_class')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga <span class="text-red-500">*</span></label>
                    <div class="relative mt-1 rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400 sm:text-sm">Rp</span>
                        </div>
                        <input
                            x-model="price"
                            type="number"
                            name="price"
                            id="price"
                            value="{{ old('price', 0) }}"
                            class="block w-full rounded-xl border-[#E9E1D5] bg-[#F8F4EC] pl-10 pr-12 focus:border-[#E15B3F] focus:ring-2 focus:ring-[#E15B3F]/30 sm:text-sm"
                            placeholder="0"
                            required
                        >
                    </div>
                    <p x-show="price > 0" class="mt-1 text-xs text-gray-500 dark:text-gray-400" x-text="'Harga yang ditampilkan: ' + formattedPrice()"></p>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kuota -->
                <div>
                    <label for="quota_avail" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kuota Tiket <span class="text-red-500">*</span></label>
                    <input
                        x-model="quota"
                        type="number"
                        name="quota_avail"
                        id="quota_avail"
                        value="{{ old('quota_avail', 100) }}"
                        class="mt-1 block w-full rounded-xl border-[#E9E1D5] bg-[#F8F4EC] shadow-sm focus:border-[#E15B3F] focus:ring-2 focus:ring-[#E15B3F]/30 sm:text-sm"
                        min="1"
                        required
                    >
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jumlah total tiket yang tersedia untuk jenis ini</p>
                    @error('quota_avail')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preview Total Pendapatan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estimasi Pendapatan</label>
                    <div class="mt-1 rounded-xl bg-[#F8F4EC] p-3">
                        <div class="flex flex-col gap-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Total pendapatan jika semua tiket terjual:
                            </p>
                            <p class="text-lg font-medium text-gray-900 dark:text-white" x-text="totalRevenue()"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi <span class="text-red-500">*</span></label>
                <textarea
                    x-model="description"
                    name="description"
                    id="description"
                    rows="4"
                    class="mt-1 block w-full rounded-xl border-[#E9E1D5] bg-[#F8F4EC] shadow-sm focus:border-[#E15B3F] focus:ring-2 focus:ring-[#E15B3F]/30 sm:text-sm"
                    required
                >{{ old('description') }}</textarea>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jelaskan detail tentang apa yang didapatkan dengan tiket ini</p>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Preview Tiket -->
            <div class="mt-6 rounded-xl bg-[#F8F4EC] p-4">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Preview Tiket</h3>
                    <div class="overflow-hidden rounded-xl border border-[#D9DEE3]">
                    <div class="bg-white dark:bg-gray-800 p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="ticketClass || 'Nama Tiket'"></h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="(description || 'Deskripsi tiket akan ditampilkan di sini').substr(0, 60) + (description && description.length > 60 ? '...' : '')"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-[#7B0015]" x-text="formattedPrice()"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="quota + ' tiket tersedia'"></p>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-[#D9DEE3] bg-[#F7ECAC]/40 p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $event->title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $event->start_event->format('d M Y, H:i') }}</p>
                            </div>
                            <div>
                                <div class="rounded-full bg-[#7B0015]/10 px-3 py-1 text-xs font-medium text-[#7B0015]">
                                    <span x-text="ticketClass || 'Tiket'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Tambahan -->
            <div class="mt-6 rounded-xl bg-[#EEF1F4] p-4">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Informasi Penting</h3>
                <ul class="list-disc pl-5 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <li>Tiket akan dijual pada periode:
                        {{ $event->start_sale->format('d M Y, H:i') }} -
                        {{ $event->end_sale->format('d M Y, H:i') }}
                    </li>
                    <li>Pastikan Anda mengatur kuota tiket dengan benar</li>
                    <li>Harga tiket tidak dapat diubah setelah ada pembelian</li>
                </ul>
            </div>

            <!-- Tombol Submit -->
            <div class="mt-6 flex justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center rounded-full border border-transparent bg-[#7B0015] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-[#E15B3F] focus:outline-none"
                >
                    <i class="fas fa-save mr-2"></i> Simpan Tiket
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
