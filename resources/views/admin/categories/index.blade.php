@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Kelola Kategori</h1>
    <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center rounded-full bg-[#7B0015] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-[#E15B3F]">
        <i class="fas fa-plus mr-2"></i> Tambah Kategori
    </a>
</div>

<!-- Filter and Search -->
<div class="mb-6 rounded-2xl border border-[#E9E1D5] bg-white p-4 shadow-sm dark:bg-gray-800">
    <div class="flex flex-col md:flex-row md:items-center gap-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Kategori</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="search" class="block w-full rounded-xl border-[#E9E1D5] bg-[#F8F4EC] py-2 pl-10 pr-3 leading-5 placeholder-gray-500 focus:border-[#E15B3F] focus:outline-none focus:ring-[#E15B3F] sm:text-sm" placeholder="Cari berdasarkan nama...">
            </div>
        </div>
    </div>
</div>

<!-- Categories Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
    @forelse ($categories as $category)
    <div class="rounded-2xl border border-[#E9E1D5] bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg dark:bg-gray-800">
        <div class="flex justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0 rounded-full bg-[#F7ECAC] p-3 text-[#7B0015]">
                    <!-- Display the icon from storage -->
                    @if($category->icon && file_exists(public_path('storage/' . $category->icon)))
                        <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="h-6 w-6 object-cover">
                    @else
                        <i class="fas fa-folder flex h-6 w-6 items-center justify-center text-[#7B0015]"></i>
                    @endif
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $category->events->count() }} Acara</p>
                </div>
            </div>
            <div class="flex items-start space-x-2">
                <a href="{{ route('admin.categories.edit', $category) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 transition-colors duration-200" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors duration-200" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="mt-3">
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $category->description ?? 'Tidak ada deskripsi' }}</p>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
        <div class="text-gray-500 dark:text-gray-400">
            <i class="fas fa-folder-open text-4xl mb-3"></i>
            <p class="text-lg">Belum ada kategori. Klik tombol Tambah Kategori untuk mulai membuat kategori.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
