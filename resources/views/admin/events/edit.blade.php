@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Acara: {{ $event->title }}</h1>
    <div class="flex space-x-2">
        <a href="{{ route('admin.tickets.index', $event) }}" class="inline-flex items-center rounded-full bg-[#7B0015] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-[#E15B3F]">
            <i class="fas fa-ticket-alt mr-2"></i> KELOLA TIKET
        </a>
        <a href="{{ route('admin.events.index') }}" class="inline-flex items-center rounded-full bg-[#F1ECE3] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-[#211F1C] transition hover:bg-[#E9E1D5]">
            <i class="fas fa-arrow-left mr-2"></i> KEMBALI
        </a>
    </div>
</div>

<div class="overflow-hidden rounded-2xl border border-[#E9E1D5] bg-white shadow-sm dark:bg-gray-800"
    x-data="{
        hasStageLayout: {{ $event->has_stage_layout ? 'true' : 'false' }},
        thumbnailPreview: '{{ $event->thumbnail ? asset('storage/' . $event->thumbnail) : '' }}',
        stageLayoutPreview: '{{ $event->stage_layout ? asset('storage/' . $event->stage_layout) : '' }}',

        handleThumbnailUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.thumbnailPreview = URL.createObjectURL(file);
            }
        },

        handleStageLayoutUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.stageLayoutPreview = URL.createObjectURL(file);
            }
        }
    }">
    <div class="p-6">
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

        <form
            id="eventForm"
            action="{{ route('admin.events.update', $event) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <!-- Section: Informasi Dasar -->
                <div class="rounded-xl bg-[#F8F4EC] p-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-info-circle mr-2"></i> Informasi Dasar
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Judul Acara -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Acara <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                name="title"
                                id="title"
                                value="{{ old('title', $event->title) }}"
                                class="mt-1 block w-full rounded-xl border-[#E9E1D5] bg-white shadow-sm focus:border-[#E15B3F] focus:ring-[#E15B3F] sm:text-sm"
                                required
                            >
                            @error('title')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori <span class="text-red-500">*</span></label>
                            <select
                                name="category_id"
                                id="category_id"
                                class="mt-1 block w-full rounded-xl border-[#E9E1D5] bg-white shadow-sm focus:border-[#E15B3F] focus:ring-[#E15B3F] sm:text-sm"
                                required
                            >
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                name="location"
                                id="location"
                                value="{{ old('location', $event->location) }}"
                                class="mt-1 block w-full rounded-xl border-[#E9E1D5] bg-white shadow-sm focus:border-[#E15B3F] focus:ring-[#E15B3F] sm:text-sm"
                                required
                            >
                            @error('location')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Thumbnail -->
                        <div>
                            <label for="thumbnail" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Thumbnail</label>
                            <div class="mt-1 flex items-center">
                                <template x-if="thumbnailPreview">
                                    <div class="mr-3 relative">
                                        <img :src="thumbnailPreview" alt="Preview" class="h-24 w-32 rounded-xl border border-[#D9DEE3] object-cover">
                                        <button
                                            type="button"
                                            @click="thumbnailPreview = ''; document.getElementById('thumbnail').value = '';"
                                            class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-[#B7372B] p-1 text-xs text-white hover:bg-red-700 focus:outline-none"
                                        >
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </template>
                                <input
                                    type="file"
                                    name="thumbnail"
                                    id="thumbnail"
                                    @change="handleThumbnailUpload"
                                    accept="image/*"
                                    class="block text-sm text-gray-500 file:mr-4 file:rounded-full file:border-0 file:bg-[#F7ECAC] file:px-4 file:py-2 file:font-semibold file:text-[#7B0015] hover:file:bg-[#E9E1D5]"
                                >
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload gambar thumbnail untuk acara (format: JPG, PNG. Maks: 2MB). Biarkan kosong jika tidak ingin mengubah.</p>
                            @error('thumbnail')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Tanggal & Waktu -->
                <div class="rounded-xl bg-[#F8F4EC] p-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-calendar-alt mr-2"></i> Tanggal & Waktu
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tanggal Mulai Acara -->
                        <div>
                            <label for="start_event" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai Acara <span class="text-red-500">*</span></label>
                            <input
                                type="datetime-local"
                                name="start_event"
                                id="start_event"
                                value="{{ old('start_event', $event->start_event ? $event->start_event->format('Y-m-d\TH:i') : '') }}"
                                class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-white shadow-sm focus:border-[#E15B3F] focus:ring-[#E15B3F] sm:text-sm"
                                required
                            >
                            @error('start_event')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Selesai Acara -->
                        <div>
                            <label for="end_event" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Selesai Acara <span class="text-red-500">*</span></label>
                            <input
                                type="datetime-local"
                                name="end_event"
                                id="end_event"
                                value="{{ old('end_event', $event->end_event ? $event->end_event->format('Y-m-d\TH:i') : '') }}"
                                class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-white shadow-sm focus:border-[#E15B3F] focus:ring-[#E15B3F] sm:text-sm"
                                required
                            >
                            @error('end_event')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Mulai Penjualan -->
                        <div>
                            <label for="start_sale" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai Penjualan <span class="text-red-500">*</span></label>
                            <input
                                type="datetime-local"
                                name="start_sale"
                                id="start_sale"
                                value="{{ old('start_sale', $event->start_sale ? $event->start_sale->format('Y-m-d\TH:i') : '') }}"
                                class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-white shadow-sm focus:border-[#E15B3F] focus:ring-[#E15B3F] sm:text-sm"
                                required
                            >
                            @error('start_sale')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Selesai Penjualan -->
                        <div>
                            <label for="end_sale" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Selesai Penjualan <span class="text-red-500">*</span></label>
                            <input
                                type="datetime-local"
                                name="end_sale"
                                id="end_sale"
                                value="{{ old('end_sale', $event->end_sale ? $event->end_sale->format('Y-m-d\TH:i') : '') }}"
                                class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-white shadow-sm focus:border-[#E15B3F] focus:ring-[#E15B3F] sm:text-sm"
                                required
                            >
                            @error('end_sale')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Timeline Visual -->
                    <div class="mt-4 rounded-xl bg-[#EEF1F4] p-4">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Timeline Acara</h3>
                        <div class="relative">
                            <div class="absolute top-4 h-1 w-full bg-[#D9DEE3]"></div>
                            <div class="flex justify-between relative">
                                <div class="text-center">
                                    <div class="mx-auto flex h-8 w-8 items-center justify-center rounded-full bg-[#F7ECAC] text-[#7B0015]">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">Mulai Penjualan</p>
                                    <p class="text-xs mt-1 font-medium text-gray-800 dark:text-gray-300">
                                        {{ $event->start_sale ? $event->start_sale->format('d M Y') : '' }}
                                    </p>
                                </div>
                                <div class="text-center">
                                    <div class="mx-auto flex h-8 w-8 items-center justify-center rounded-full bg-[#FDE8E7] text-[#B7372B]">
                                        <i class="fas fa-ticket-alt"></i>
                                    </div>
                                    <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">Akhir Penjualan</p>
                                    <p class="text-xs mt-1 font-medium text-gray-800 dark:text-gray-300">
                                        {{ $event->end_sale ? $event->end_sale->format('d M Y') : '' }}
                                    </p>
                                </div>
                                <div class="text-center">
                                    <div class="mx-auto flex h-8 w-8 items-center justify-center rounded-full bg-[#E5F4EC] text-[#2F855A]">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">Mulai Acara</p>
                                    <p class="text-xs mt-1 font-medium text-gray-800 dark:text-gray-300">
                                        {{ $event->start_event ? $event->start_event->format('d M Y') : '' }}
                                    </p>
                                </div>
                                <div class="text-center">
                                    <div class="mx-auto flex h-8 w-8 items-center justify-center rounded-full bg-[#D9DEE3] text-[#52606D]">
                                        <i class="fas fa-flag-checkered"></i>
                                    </div>
                                    <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">Akhir Acara</p>
                                    <p class="text-xs mt-1 font-medium text-gray-800 dark:text-gray-300">
                                        {{ $event->end_event ? $event->end_event->format('d M Y') : '' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Panel -->
                    <div class="mt-4 rounded-xl bg-[#EEF1F4] p-4">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Informasi Waktu</h3>
                        <ul class="list-disc pl-5 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <li>Pastikan tanggal mulai acara lebih awal dari tanggal selesai acara</li>
                            <li>Periode penjualan tiket harus sebelum tanggal mulai acara</li>
                            <li>Pengguna dapat membeli tiket selama periode penjualan yang ditentukan</li>
                        </ul>
                    </div>
                </div>

                <!-- Section: Detail & Layout -->
                <div class="rounded-xl bg-[#F8FAFB] p-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-align-left mr-2"></i> Detail & Layout
                    </h2>

                    <!-- Deskripsi -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi <span class="text-red-500">*</span></label>
                        <textarea
                            name="description"
                            id="description"
                            rows="6"
                            class="mt-1 block w-full rounded-xl border-[#D9DEE3] bg-white shadow-sm focus:border-[#E15B3F] focus:ring-[#E15B3F] sm:text-sm"
                            required
                        >{{ old('description', $event->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stage Layout Option -->
                    <div class="mt-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input
                                    x-model="hasStageLayout"
                                    id="has_stage_layout"
                                    name="has_stage_layout"
                                    type="checkbox"
                                    value="1"
                                    class="h-4 w-4 rounded border-[#D9DEE3] text-[#7B0015] focus:ring-[#E15B3F]"
                                >
                                <!-- Input hidden untuk nilai false -->
                                <input type="hidden" name="has_stage_layout" value="0">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="has_stage_layout" class="font-medium text-gray-700 dark:text-gray-300">Acara Menggunakan Layout Panggung</label>
                                <p class="text-gray-500 dark:text-gray-400">Centang jika acara memiliki layout panggung atau denah kursi (venue) yang perlu diperlihatkan kepada pembeli tiket</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stage Layout Upload -->
                    <div
                        x-show="hasStageLayout"
                        x-transition
                        class="mt-4 rounded-xl bg-[#EEF1F4] p-4"
                    >
                        <label for="stage_layout" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Layout Panggung <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex items-center">
                            <template x-if="stageLayoutPreview">
                                <div class="mr-3 relative">
                                    <img :src="stageLayoutPreview" alt="Stage Layout" class="h-32 w-40 rounded-xl border border-[#D9DEE3] object-contain">
                                    <button
                                        type="button"
                                        @click="stageLayoutPreview = ''; document.getElementById('stage_layout').value = '';"
                                        class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-[#B7372B] p-1 text-xs text-white hover:bg-red-700 focus:outline-none"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </template>
                            <input
                                type="file"
                                name="stage_layout"
                                id="stage_layout"
                                @change="handleStageLayoutUpload"
                                accept="image/*"
                                class="block text-sm text-gray-500 file:mr-4 file:rounded-full file:border-0 file:bg-[#F7ECAC] file:px-4 file:py-2 file:font-semibold file:text-[#7B0015] hover:file:bg-[#E9E1D5]"
                                x-bind:required="hasStageLayout"
                            >
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload gambar layout panggung/venue (format: JPG, PNG. Maks: 2MB)</p>
                        @error('stage_layout')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Preview Acara -->
                <div class="rounded-xl bg-[#F8FAFB] p-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-eye mr-2"></i> Preview Tampilan Detail Acara
                    </h2>

                    <div class="relative rounded-xl overflow-hidden shadow-md bg-gradient-to-r from-[#7B0015] to-[#AF0020]">
                        <!-- Header section with controlled height -->
                        <div class="flex flex-col md:flex-row items-center">
                            <!-- Thumbnail dengan ukuran terkontrol di sisi kiri (hanya pada desktop) -->
                            <template x-if="thumbnailPreview">
                                <div class="hidden md:block md:w-1/3 h-40 overflow-hidden">
                                    <div class="w-full h-full relative">
                                        <img :src="thumbnailPreview"
                                            alt="{{ $event->title }}"
                                            class="object-contain w-full h-full p-2" />
                                    </div>
                                </div>
                            </template>

                            <!-- Content di sisi kanan (atau penuh pada mobile) -->
                            <div class="p-6 md:p-8" :class="thumbnailPreview ? 'md:w-2/3' : 'w-full'">
                                <div>
                                    <div class="flex items-start justify-between mb-4">
                                        <span class="inline-block bg-white/20 text-white text-xs px-2 py-1 rounded-full">
                                            {{ $event->category->name ?? 'Kategori' }}
                                        </span>

                                        @php
                                            $now = now();
                                            $isUpcoming = $now < $event->start_event;
                                            $isOngoing = $now >= $event->start_event && $now <= $event->end_event;
                                            $isPast = $now > $event->end_event;
                                        @endphp

                                        <span class="inline-block
                                            {{ $isUpcoming ? 'bg-blue-600' : ($isOngoing ? 'bg-green-600' : 'bg-gray-600') }}
                                            text-white text-xs px-2 py-1 rounded">
                                            {{ $isUpcoming ? 'Akan Datang' : ($isOngoing ? 'Sedang Berlangsung' : 'Selesai') }}
                                        </span>
                                    </div>

                                    <h1 class="text-xl md:text-2xl font-bold text-white mb-2">{{ $event->title }}</h1>

                                    <div class="flex flex-wrap text-white/80 text-sm gap-4 mt-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-alt mr-2"></i>
                                            <span>{{ $event->start_event ? $event->start_event->format('d F Y') : 'Tanggal Acara' }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock mr-2"></i>
                                            <span>{{ $event->start_event ? $event->start_event->format('H:i') : 'Waktu' }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt mr-2"></i>
                                            <span>{{ $event->location }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile thumbnail yang lebih kecil (hanya tampil di mobile) -->
                        <template x-if="thumbnailPreview">
                            <div class="relative h-40 w-full overflow-hidden bg-[#EEF1F4] md:hidden">
                                <img :src="thumbnailPreview"
                                    alt="{{ $event->title }}"
                                    class="object-contain w-full h-full" />
                            </div>
                        </template>
                    </div>

                    <!-- Stage Layout Preview (if enabled) -->
                    <template x-if="hasStageLayout && stageLayoutPreview">
                        <div class="mt-4 rounded-xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Layout Venue</h4>
                            <img :src="stageLayoutPreview" alt="Layout Venue" class="w-full h-auto rounded-lg" />
                        </div>
                    </template>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="mt-8 flex justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center rounded-full border border-transparent bg-[#2F855A] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-[#276749] focus:outline-none"
                >
                    <i class="fas fa-save mr-2"></i> Perbarui Acara
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
