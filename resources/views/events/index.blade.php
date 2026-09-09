<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Event4U</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans">

    @include('components.navbar')

    <main class="container mx-auto mb-16 mt-8 px-4 lg:px-6">
        <!-- Header Section -->
        <div class="relative rounded-xl overflow-hidden shadow-xl mb-8 bg-gradient-to-r from-[#7B0015] to-[#AF0020] h-48 md:h-64">
            <div class="absolute inset-0 flex items-center px-8 md:px-16">
                <div class="max-w-3xl">
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Discover Events</h1>
                    <p class="text-white/80 md:text-lg">Find and book your favorite events in just a few clicks</p>
                </div>
            </div>
        </div>

        <!-- Category Pills (Horizontal Scrollable) -->
        <div class="mb-6 overflow-hidden">
            <div class="flex items-center space-x-2 mb-2">
                <h3 class="text-lg font-semibold">Categories:</h3>
                <button id="show-all-categories" class="text-sm text-[#7B0015] hover:text-[#950019] font-medium flex items-center">
                    <span id="toggle-text">Show All</span>
                    <i class="fas fa-chevron-down ml-1" id="toggle-icon"></i>
                </button>
            </div>

            <div class="flex space-x-2 overflow-x-auto pb-3 scrollbar-hide" id="category-scroll">
                <a href="{{ route('events.index') }}"
                   class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ !request('category_id') ? 'bg-[#7B0015] text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                    All Events
                </a>

                @foreach($categories->take(8) as $category)
                    <a href="{{ route('events.index', ['category_id' => $category->id]) }}"
                       class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ request('category_id') == $category->id ? 'bg-[#7B0015] text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        {{ $category->name }}
                    </a>
                @endforeach

                @if($categories->count() > 8)
                    <button id="more-categories-btn" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-gray-200 text-gray-800 hover:bg-gray-300">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                @endif
            </div>
        </div>

        <!-- Categories Grid (Initially Hidden) -->
        <div id="categories-grid" class="hidden bg-white rounded-xl shadow-md mb-8 p-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($categories as $category)
                    <a href="{{ route('events.index', ['category_id' => $category->id]) }}"
                        class="bg-white border rounded-lg p-3 text-center hover:shadow-md transition hover:bg-gray-50 {{ request('category_id') == $category->id ? 'border-[#7B0015] ring-1 ring-[#7B0015]' : 'border-gray-200' }}">
                        <div class="w-12 h-12 mx-auto rounded-full bg-[#7B0015]/10 flex items-center justify-center text-[#7B0015] mb-2">
                            @php
                                // Logic untuk menentukan ikon berdasarkan nama kategori
                                $name = strtolower($category->name);
                                $icon = 'calendar-alt';

                                if (strpos($name, 'konser') !== false || strpos($name, 'musik') !== false) {
                                    $icon = 'music';
                                } elseif (strpos($name, 'seminar') !== false) {
                                    $icon = 'microphone';
                                } elseif (strpos($name, 'festival') !== false) {
                                    $icon = 'child';
                                } elseif (strpos($name, 'workshop') !== false) {
                                    $icon = 'tools';
                                } elseif (strpos($name, 'pameran') !== false) {
                                    $icon = 'image';
                                } elseif (strpos($name, 'kompetisi') !== false) {
                                    $icon = 'trophy';
                                } elseif (strpos($name, 'teater') !== false || strpos($name, 'pertunjukan') !== false) {
                                    $icon = 'theater-masks';
                                } elseif (strpos($name, 'olahraga') !== false || strpos($name, 'sport') !== false) {
                                    $icon = 'running';
                                }
                            @endphp

                            @if($category->icon && file_exists(public_path('storage/' . $category->icon)))
                                <div class="w-full h-full flex items-center justify-center bg-[#7B0015]/10 rounded-full">
                                    <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}"
                                         class="w-6 h-6 object-contain">
                                </div>
                            @else
                                <i class="fas fa-{{ $icon }}"></i>
                            @endif
                        </div>

                        <h3 class="text-sm font-medium text-gray-900 line-clamp-1">{{ $category->name }}</h3>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-md mb-8 p-4 md:p-6">
            <form action="{{ route('events.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:flex-wrap md:items-end md:gap-4">
                <!-- Search -->
                <div class="md:w-1/3 lg:w-1/4 xl:w-1/5">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Search events..."
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#7B0015] focus:ring-[#7B0015]">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="md:w-1/4 lg:w-1/5">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="category_id" name="category_id"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#7B0015] focus:ring-[#7B0015]">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="md:w-1/4 lg:w-1/5">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#7B0015] focus:ring-[#7B0015]">
                        <option value="">All Events</option>
                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="past" {{ request('status') == 'past' ? 'selected' : '' }}>Past Events</option>
                    </select>
                </div>

                <!-- Filter Button -->
                <div class="md:flex-grow">
                    <button type="submit" class="w-full md:w-auto bg-[#7B0015] hover:bg-[#950019] text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-filter mr-2"></i>Filter Results
                    </button>
                </div>

                <!-- Clear Filter Button -->
                @if(request('search') || request('category_id') || request('status'))
                <div>
                    <a href="{{ route('events.index') }}" class="inline-block w-full md:w-auto bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition text-center">
                        <i class="fas fa-times mr-2"></i>Clear Filters
                    </a>
                </div>
                @endif
            </form>
        </div>

        <!-- Results Section -->
        <div class="mb-8">
            <div class="flex flex-wrap justify-between items-center mb-6">
                <h2 class="text-2xl md:text-3xl font-bold">
                    <span>
                        @if(request('search'))
                            Search results for "{{ request('search') }}"
                        @elseif(request('category_id'))
                            {{ $categories->where('id', request('category_id'))->first()->name ?? 'Events' }}
                        @elseif(request('status') == 'upcoming')
                            Upcoming Events
                        @elseif(request('status') == 'ongoing')
                            Ongoing Events
                        @elseif(request('status') == 'past')
                            Past Events
                        @else
                            All Events
                        @endif
                    </span>
                    <span class="block h-1 w-24 bg-[#7B0015] mt-2"></span>
                </h2>
                <div class="text-sm text-gray-500 mt-2 md:mt-0">
                    Found {{ $events->count() }} events
                </div>
            </div>

            @if($events->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach($events as $event)
                        <x-event-card :event="$event" />
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow p-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <i class="fas fa-calendar-times text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-700 mb-2">No events found</h3>
                        <p class="text-gray-500 mb-6">We couldn't find any events matching your criteria.</p>
                        <a href="{{ route('events.index') }}" class="bg-[#7B0015] hover:bg-[#950019] text-white px-4 py-2 rounded-lg transition">
                            View All Events
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </main>

    @include('components.footer')

    <style>
    /* Hide scrollbar but maintain functionality */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle categories grid visibility
            const showAllBtn = document.getElementById('show-all-categories');
            const categoriesGrid = document.getElementById('categories-grid');
            const toggleText = document.getElementById('toggle-text');
            const toggleIcon = document.getElementById('toggle-icon');

            showAllBtn.addEventListener('click', function() {
                if (categoriesGrid.classList.contains('hidden')) {
                    categoriesGrid.classList.remove('hidden');
                    toggleText.textContent = 'Hide';
                    toggleIcon.classList.remove('fa-chevron-down');
                    toggleIcon.classList.add('fa-chevron-up');
                } else {
                    categoriesGrid.classList.add('hidden');
                    toggleText.textContent = 'Show All';
                    toggleIcon.classList.remove('fa-chevron-up');
                    toggleIcon.classList.add('fa-chevron-down');
                }
            });

            // More categories button
            const moreBtn = document.getElementById('more-categories-btn');
            if (moreBtn) {
                moreBtn.addEventListener('click', function() {
                    if (categoriesGrid.classList.contains('hidden')) {
                        categoriesGrid.classList.remove('hidden');
                        toggleText.textContent = 'Hide';
                        toggleIcon.classList.remove('fa-chevron-down');
                        toggleIcon.classList.add('fa-chevron-up');
                    }
                });
            }
        });
    </script>
</body>
</html>
