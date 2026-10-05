@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
        {{ isset($event) ? 'Edit Acara: ' . $event->title : 'Tambah Acara Baru' }}
    </h1>
    <a href="{{ route('admin.events.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
        <i class="fas fa-arrow-left mr-2"></i> Kembali
    </a>
</div>

<div
    x-data="{
        title: '{{ old('title', $event->title ?? '') }}',
        description: '{{ old('description', $event->description ?? '') }}',
        location: '{{ old('location', $event->location ?? '') }}',
        category_id: '{{ old('category_id', $event->category_id ?? '') }}',
        start_event: '{{ old('start_event', isset($event) ? $event->start_event->format('Y-m-d\TH:i') : '') }}',
        end_event: '{{ old('end_event', isset($event) ? $event->end_event->format('Y-m-d\TH:i') : '') }}',
        start_sale: '{{ old('start_sale', isset($event) ? $event->start_sale->format('Y-m-d\TH:i') : '') }}',
        end_sale: '{{ old('end_sale', isset($event) ? $event->end_sale->format('Y-m-d\TH:i') : '') }}',
        hasStageLayout: {{ old('has_stage_layout', isset($event) && $event->stage_layout ? 'true' : 'false') }},
        thumbnailPreview: '{{ isset($event) && $event->thumbnail ? asset('storage/' . $event->thumbnail) : '' }}',
        stageLayoutPreview: '{{ isset($event) && $event->stage_layout ? asset('storage/' . $event->stage_layout) : '' }}',
        currentStep: 1,
        totalSteps: 3,

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
        },

        nextStep() {
            if (this.validateStep(this.currentStep)) {
                this.currentStep++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        prevStep() {
            this.currentStep--;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        validateStep(step) {
            let isValid = true;

            // Reset all validation errors
            document.querySelectorAll('[x-ref^=\'error\']').forEach(el => {
                el.textContent = '';
            });

            if (step === 1) {
                // Validate step 1: Basic information
                if (!this.title.trim()) {
                    this.$refs.errorTitle.textContent = 'Judul acara tidak boleh kosong';
                    isValid = false;
                }

                if (!this.category_id) {
                    this.$refs.errorCategory.textContent = 'Kategori harus dipilih';
                    isValid = false;
                }

                if (!this.location.trim()) {
                    this.$refs.errorLocation.textContent = 'Lokasi tidak boleh kosong';
                    isValid = false;
                }

                if (!this.thumbnailPreview && !document.getElementById('thumbnail').files[0]) {
                    this.$refs.errorThumbnail.textContent = 'Thumbnail acara wajib diupload';
                    isValid = false;
                }
            } else if (step === 2) {
                // Validate step 2: Date and time
                if (!this.start_event) {
                    this.$refs.errorStartEvent.textContent = 'Tanggal mulai acara harus diisi';
                    isValid = false;
                }

                if (!this.end_event) {
                    this.$refs.errorEndEvent.textContent = 'Tanggal selesai acara harus diisi';
                    isValid = false;
                }

                if (this.start_event && this.end_event && new Date(this.start_event) >= new Date(this.end_event)) {
                    this.$refs.errorEndEvent.textContent = 'Tanggal selesai harus setelah tanggal mulai acara';
                    isValid = false;
                }

                if (!this.start_sale) {
                    this.$refs.errorStartSale.textContent = 'Tanggal mulai penjualan harus diisi';
                    isValid = false;
                }

                if (!this.end_sale) {
                    this.$refs.errorEndSale.textContent = 'Tanggal selesai penjualan harus diisi';
                    isValid = false;
                }

                if (this.start_sale && this.end_sale && new Date(this.start_sale) >= new Date(this.end_sale)) {
                    this.$refs.errorEndSale.textContent = 'Tanggal selesai harus setelah tanggal mulai penjualan';
                    isValid = false;
                }

                if (this.end_sale && this.start_event && new Date(this.end_sale) > new Date(this.start_event)) {
                    this.$refs.errorEndSale.textContent = 'Tanggal selesai penjualan harus sebelum tanggal mulai acara';
                    isValid = false;
                }
            } else if (step === 3) {
                // Validate step 3: Description and stage layout
                if (!this.description.trim()) {
                    this.$refs.errorDescription.textContent = 'Deskripsi acara tidak boleh kosong';
                    isValid = false;
                }

                if (this.hasStageLayout && !this.stageLayoutPreview && !document.getElementById('stage_layout').files[0]) {
                    this.$refs.errorStageLayout.textContent = 'Layout panggung wajib diupload jika opsi ini dipilih';
                    isValid = false;
                }
            }

            return isValid;
        },

        submitForm() {
            if (this.validateStep(this.currentStep)) {
                document.getElementById('eventForm').submit();
            }
        }
    }"
    class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden"
>
    <div class="p-6">
        <!-- Stepper -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div
                    @click="currentStep = 1"
                    class="flex-1 cursor-pointer"
                    :class="{'cursor-pointer': currentStep > 1, 'cursor-default': currentStep === 1}"
                >
                    <div class="flex items-center">
                        <div
                            :class="currentStep >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300'"
                            class="flex items-center justify-center w-8 h-8 rounded-full font-bold"
                        >
                            1
                        </div>
                        <div class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Informasi Dasar</div>
                    </div>
                </div>
                <div class="w-full flex-1 h-1 mx-2 bg-gray-200 dark:bg-gray-700">
                    <div
                        class="h-1 bg-indigo-600 transition-all duration-500 ease-in-out"
                        :style="'width: ' + (currentStep > 1 ? '100%' : '0')"
                    ></div>
                </div>
                <div
                    @click="currentStep > 1 ? currentStep = 2 : null"
                    class="flex-1"
                    :class="{'cursor-pointer': currentStep > 2, 'cursor-default': currentStep <= 2}"
                >
                    <div class="flex items-center">
                        <div
                            :class="currentStep >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300'"
                            class="flex items-center justify-center w-8 h-8 rounded-full font-bold"
                        >
                            2
                        </div>
                        <div class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal & Waktu</div>
                    </div>
                </div>
                <div class="w-full flex-1 h-1 mx-2 bg-gray-200 dark:bg-gray-700">
                    <div
                        class="h-1 bg-indigo-600 transition-all duration-500 ease-in-out"
                        :style="'width: ' + (currentStep > 2 ? '100%' : '0')"
                    ></div>
                </div>
                <div
                    @click="currentStep > 2 ? currentStep = 3 : null"
                    class="flex-1"
                    :class="{'cursor-pointer': currentStep === 3, 'cursor-default': currentStep < 3}"
                >
                    <div class="flex items-center">
                        <div
                            :class="currentStep >= 3 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300'"
                            class="flex items-center justify-center w-8 h-8 rounded-full font-bold"
                        >
                            3
                        </div>
                        <div class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Detail & Layout</div>
                    </div>
                </div>
            </div>
        </div>

        <form
            id="eventForm"
            action="{{ isset($event) ? route('admin.events.update', $event) : route('admin.events.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @if(isset($event))
                @method('PUT')
            @endif

            <!-- Step 1: Basic Information -->
            <div x-show="currentStep === 1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Judul Acara -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Acara <span class="text-red-500">*</span></label>
                        <input
                            x-model="title"
                            type="text"
                            name="title"
                            id="title"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-[#E15B3F]/30 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                            required
                        >
                        <p x-ref="errorTitle" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori <span class="text-red-500">*</span></label>
                        <select
                            x-model="category_id"
                            name="category_id"
                            id="category_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-[#E15B3F]/30 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                            required
                        >
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', isset($event) ? $event->category_id : '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <p x-ref="errorCategory" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lokasi -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi <span class="text-red-500">*</span></label>
                        <input
                            x-model="location"
                            type="text"
                            name="location"
                            id="location"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-[#E15B3F]/30 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                            required
                        >
                        <p x-ref="errorLocation" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                        @error('location')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Thumbnail -->
                    <div>
                        <label for="thumbnail" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Thumbnail <span class="text-red-500">{{ isset($event) ? '' : '*' }}</span>
                        </label>
                        <div class="mt-1 flex items-center">
                            <template x-if="thumbnailPreview">
                                <div class="mr-3 relative">
                                    <img :src="thumbnailPreview" alt="Preview" class="h-24 w-32 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                                    <button
                                        type="button"
                                        @click="thumbnailPreview = ''; document.getElementById('thumbnail').value = '';"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 focus:outline-none"
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
                                class="block text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800"
                                {{ isset($event) ? '' : 'required' }}
                            >
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload gambar thumbnail untuk acara (format: JPG, PNG. Maks: 2MB)</p>
                        <p x-ref="errorThumbnail" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                        @error('thumbnail')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Step 2: Date and Time -->
            <div x-show="currentStep === 2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tanggal Mulai Acara -->
                    <div>
                        <label for="start_event" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai Acara <span class="text-red-500">*</span></label>
                        <input
                            x-model="start_event"
                            type="datetime-local"
                            name="start_event"
                            id="start_event"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-[#E15B3F]/30 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                            required
                        >
                        <p x-ref="errorStartEvent" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                        @error('start_event')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Selesai Acara -->
                    <div>
                        <label for="end_event" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Selesai Acara <span class="text-red-500">*</span></label>
                        <input
                            x-model="end_event"
                            type="datetime-local"
                            name="end_event"
                            id="end_event"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-[#E15B3F]/30 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                            required
                        >
                        <p x-ref="errorEndEvent" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                        @error('end_event')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Mulai Penjualan -->
                    <div>
                        <label for="start_sale" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai Penjualan <span class="text-red-500">*</span></label>
                        <input
                            x-model="start_sale"
                            type="datetime-local"
                            name="start_sale"
                            id="start_sale"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-[#E15B3F]/30 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                            required
                        >
                        <p x-ref="errorStartSale" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                        @error('start_sale')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Selesai Penjualan -->
                    <div>
                        <label for="end_sale" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Selesai Penjualan <span class="text-red-500">*</span></label>
                        <input
                            x-model="end_sale"
                            type="datetime-local"
                            name="end_sale"
                            id="end_sale"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-[#E15B3F]/30 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                            required
                        >
                        <p x-ref="errorEndSale" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                        @error('end_sale')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Info Panel -->
                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Informasi Waktu</h3>
                    <ul class="list-disc pl-5 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li>Pastikan tanggal mulai acara lebih awal dari tanggal selesai acara</li>
                        <li>Periode penjualan tiket harus sebelum tanggal mulai acara</li>
                        <li>Pengguna dapat membeli tiket selama periode penjualan yang ditentukan</li>
                    </ul>
                </div>

                <!-- Timeline Visual -->
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Timeline Acara</h3>
                    <div class="relative">
                        <div class="h-1 bg-gray-200 dark:bg-gray-600 absolute w-full top-4"></div>
                        <div class="flex justify-between relative">
                            <div
                                class="text-center"
                                x-show="start_sale"
                            >
                                <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center mx-auto">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">Mulai Penjualan</p>
                                <p class="text-xs mt-1 font-medium text-gray-800 dark:text-gray-300" x-text="start_sale ? new Date(start_sale).toLocaleDateString() : ''"></p>
                            </div>
                            <div
                                class="text-center"
                                x-show="end_sale"
                            >
                                <div class="w-8 h-8 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center mx-auto">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">Akhir Penjualan</p>
                                <p class="text-xs mt-1 font-medium text-gray-800 dark:text-gray-300" x-text="end_sale ? new Date(end_sale).toLocaleDateString() : ''"></p>
                            </div>
                            <div
                                class="text-center"
                                x-show="start_event"
                            >
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mx-auto">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">Mulai Acara</p>
                                <p class="text-xs mt-1 font-medium text-gray-800 dark:text-gray-300" x-text="start_event ? new Date(start_event).toLocaleDateString() : ''"></p>
                            </div>
                            <div
                                class="text-center"
                                x-show="end_event"
                            >
                                <div class="w-8 h-8 bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center mx-auto">
                                    <i class="fas fa-flag-checkered"></i>
                                </div>
                                <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">Akhir Acara</p>
                                <p class="text-xs mt-1 font-medium text-gray-800 dark:text-gray-300" x-text="end_event ? new Date(end_event).toLocaleDateString() : ''"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Description and Stage Layout -->
            <div x-show="currentStep === 3">
                <!-- Deskripsi -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi <span class="text-red-500">*</span></label>
                    <textarea
                        x-model="description"
                        name="description"
                        id="description"
                        rows="6"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-[#E15B3F]/30 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                        required
                    ></textarea>
                    <p x-ref="errorDescription" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
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
                                class="focus:ring-[#E15B3F]/30 h-4 w-4 text-indigo-600 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                                @if(old('has_stage_layout', isset($event) ? $event->has_stage_layout : false)) checked @endif
                            >
                            <!-- Tambahkan input hidden untuk nilai false -->
                            <input type="hidden" name="has_stage_layout" value="0">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="has_stage_layout" class="font-medium text-gray-700 dark:text-gray-300">Acara Menggunakan Layout Panggung</label>
                            <p class="text-gray-500 dark:text-gray-400">Centang jika acara memiliki layout panggung atau denah kursi</p>
                        </div>
                    </div>
                </div>

                <!-- Stage Layout Upload -->
                <div
                    x-show="hasStageLayout"
                    x-transition
                    class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg"
                >
                    <label for="stage_layout" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Layout Panggung <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 flex items-center">
                        <template x-if="stageLayoutPreview">
                            <div class="mr-3 relative">
                                <img :src="stageLayoutPreview" alt="Stage Layout" class="h-32 w-40 object-contain rounded-lg border border-gray-300 dark:border-gray-600">
                                <button
                                    type="button"
                                    @click="stageLayoutPreview = ''; document.getElementById('stage_layout').value = '';"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 focus:outline-none"
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
                            class="block text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800"
                            x-bind:required="hasStageLayout"
                        >
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload gambar layout panggung/venue (format: JPG, PNG. Maks: 2MB)</p>
                    @error('stage_layout')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Event Preview -->
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Preview Tampilan Detail Acara</h3>

                    <div class="relative rounded-xl overflow-hidden shadow-md bg-gradient-to-r from-[#7B0015] to-[#AF0020]">
                        <!-- Header section with controlled height -->
                        <div class="flex flex-col md:flex-row items-center">
                            <!-- Thumbnail dengan ukuran terkontrol di sisi kiri (hanya pada desktop) -->
                            <template x-if="thumbnailPreview">
                                <div class="hidden md:block md:w-1/3 h-40 overflow-hidden">
                                    <div class="w-full h-full relative">
                                        <img :src="thumbnailPreview"
                                            alt="Preview Thumbnail"
                                            class="object-contain w-full h-full p-2" />
                                    </div>
                                </div>
                            </template>

                            <!-- Content di sisi kanan (atau penuh pada mobile) -->
                            <div class="p-6 md:p-8" :class="thumbnailPreview ? 'md:w-2/3' : 'w-full'">
                                <div>
                                    <div class="flex items-start justify-between mb-4">
                                        <span class="inline-block bg-white/20 text-white text-xs px-2 py-1 rounded-full">
                                            {{ isset($category_id) && $category_id ? $categories->firstWhere('id', $category_id)->name : 'Kategori' }}
                                        </span>
                                        <span class="inline-block bg-blue-600 text-white text-xs px-2 py-1 rounded">
                                            Akan Datang
                                        </span>
                                    </div>

                                    <h1 class="text-xl md:text-2xl font-bold text-white mb-2" x-text="title || 'Judul Acara'"></h1>

                                    <div class="flex flex-wrap text-white/80 text-sm gap-4 mt-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-alt mr-2"></i>
                                            <span x-text="start_event ? new Date(start_event).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : 'Tanggal Acara'"></span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock mr-2"></i>
                                            <span x-text="start_event ? new Date(start_event).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) : 'Waktu'"></span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt mr-2"></i>
                                            <span x-text="location || 'Lokasi'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile thumbnail yang lebih kecil (hanya tampil di mobile) -->
                        <template x-if="thumbnailPreview">
                            <div class="md:hidden w-full h-40 bg-gray-100 overflow-hidden relative">
                                <img :src="thumbnailPreview"
                                    alt="Preview Thumbnail"
                                    class="object-contain w-full h-full" />
                            </div>
                        </template>
                    </div>

                    <!-- Stage Layout Preview (if enabled) -->
                    <template x-if="hasStageLayout && stageLayoutPreview">
                        <div class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Layout Venue</h4>
                            <img :src="stageLayoutPreview" alt="Layout Venue" class="w-full h-auto rounded-lg" />
                        </div>
                    </template>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="mt-8 flex justify-between">
                <button
                    x-show="currentStep > 1"
                    type="button"
                    @click="prevStep"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <i class="fas fa-arrow-left mr-2"></i> Sebelumnya
                </button>

                <span x-show="currentStep === 1"></span>

                <template x-if="currentStep < totalSteps">
                    <button
                        type="button"
                        @click="nextStep"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-[#E15B3F]/30 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Selanjutnya <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </template>
                <template x-if="currentStep === totalSteps">
                    <button
                        type="button"
                        @click="submitForm"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <i class="fas fa-save mr-2"></i> {{ isset($event) ? 'Perbarui Acara' : 'Simpan Acara' }}
                    </button>
                </template>
            </div>
        </form>
    </div>
</div>
@endsection
