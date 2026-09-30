@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Pilih Acara</h1>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pilih acara untuk mengelola jenis tiket, kuota, dan harga.</p>
</div>

<div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
    @forelse($events as $event)
        <article class="overflow-hidden rounded-2xl border border-[#E9E1D5] bg-white shadow-sm dark:bg-gray-800">
            @if($event->thumbnail)
                <img src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}" class="h-44 w-full object-cover">
            @else
                <div class="flex h-44 items-center justify-center bg-[#F8F4EC] dark:bg-gray-700">
                    <i class="fas fa-calendar-alt text-4xl text-gray-400 dark:text-gray-500"></i>
                </div>
            @endif

            <div class="p-5">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $event->title }}</h2>
                    <span class="shrink-0 rounded-full bg-[#F1ECE3] px-2.5 py-1 text-xs font-medium text-[#7B0015] dark:bg-gray-700 dark:text-[#F7ECAC]">
                        {{ $event->tickets->count() }} tiket
                    </span>
                </div>

                <dl class="mt-4 space-y-2 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-map-marker-alt mt-0.5 w-4 text-center"></i>
                        <dd>{{ $event->location }}</dd>
                    </div>
                    <div class="flex items-start gap-2">
                        <i class="fas fa-calendar-day mt-0.5 w-4 text-center"></i>
                        <dd>{{ $event->start_event->format('d M Y, H:i') }}</dd>
                    </div>
                    @if($event->category)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-tag mt-0.5 w-4 text-center"></i>
                            <dd>{{ $event->category->name }}</dd>
                        </div>
                    @endif
                </dl>

                <a href="{{ route('admin.tickets.index', $event) }}" class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-[#7B0015] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-[#E15B3F]">
                    <i class="fas fa-ticket-alt mr-2"></i> Kelola Tiket
                </a>
            </div>
        </article>
    @empty
        <div class="col-span-full rounded-2xl border border-dashed border-[#D9DEE3] bg-white p-10 text-center shadow-sm dark:bg-gray-800">
            <i class="fas fa-calendar-times text-4xl text-gray-400 dark:text-gray-500"></i>
            <h2 class="mt-3 text-lg font-semibold text-gray-900 dark:text-white">Belum ada acara</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Buat acara terlebih dahulu sebelum menambahkan tiket.</p>
            <a href="{{ route('admin.events.create') }}" class="mt-5 inline-flex items-center rounded-full bg-[#7B0015] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-[#E15B3F]">
                <i class="fas fa-plus mr-2"></i> Tambah Acara
            </a>
        </div>
    @endforelse
</div>
@endsection
