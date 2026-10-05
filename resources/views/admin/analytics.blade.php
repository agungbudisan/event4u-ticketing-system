@extends('admin.layouts.app')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Analitik Acara</h1>

    <!-- Tombol Export ke Excel dan PDF -->
    <div class="flex space-x-4">
        {{-- Tombol Export ke Excel --}}
        <a href="{{ route('admin.analytics.exportExcel', ['eventId' => $event->id ?? null]) }}"
            class="rounded-full bg-[#2F855A] px-5 py-2.5 font-bold text-white shadow-sm transition-colors duration-300 hover:bg-[#276749] focus:outline-none">
            <i class="fas fa-file-excel mr-2"></i>Export to Excel
        </a>

        {{-- Tombol Export ke PDF --}}
        <a href="{{ route('admin.analytics.exportPdf', ['eventId' => $event->id ?? null]) }}"
            class="rounded-full bg-[#7B0015] px-5 py-2.5 font-bold text-white shadow-sm transition-colors duration-300 hover:bg-[#E15B3F] focus:outline-none">
            <i class="fas fa-file-pdf mr-2"></i>Export to PDF
        </a>
    </div>

    <!-- Event Selector -->
    <div class="w-full md:w-auto">
        <select id="eventSelect" class="block w-full rounded-xl border-[#D9DEE3] bg-white shadow-sm focus:border-[#E15B3F] focus:ring-2 focus:ring-[#E15B3F]/30 sm:text-sm" onchange="selectEvent(this.value)">
            <option value="">Pilih Acara</option>
            @foreach($events as $evt)
                <option value="{{ $evt->id }}" {{ isset($event) && $event->id == $evt->id ? 'selected' : '' }}>
                    {{ $evt->title }}
                </option>
            @endforeach
        </select>
    </div>
</div>

@if(isset($event))
    <!-- Event Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Sales -->
        <div class="rounded-2xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-white"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Penjualan</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Tickets Sold -->
        <div class="rounded-2xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-ticket-alt text-white"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tiket Terjual</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalTicketsSold }}</p>
                </div>
            </div>
        </div>

        <!-- Event Category -->
        <div class="rounded-2xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                    @if(isset($event->category) && isset($event->category->icon))
                        <img src="{{ asset('storage/' . $event->category->icon) }}" alt="{{ $event->category->name }}" class="h-6 w-6 object-cover">
                    @else
                        <i class="fas fa-tag text-white"></i>
                    @endif
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kategori</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $event->category->name ?? 'Tidak ada kategori' }}</p>
                </div>
            </div>
        </div>

        <!-- Event Date -->
        <div class="rounded-2xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-white"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Acara</p>
                    <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $event->start_event->format('d M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Sales Chart -->
        <div class="lg:col-span-2 rounded-2xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Penjualan Tiket per Jenis</h2>
            <div id="sales-chart" class="h-80"></div>
        </div>

        <!-- Ticket Type Breakdown -->
        <div class="rounded-2xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Rincian Jenis Tiket</h2>

            @foreach($ticketTypes as $ticket)
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $ticket['name'] }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $ticket['sold'] }}/{{ $ticket['quota'] }}</span>
                    </div>
                    <div class="h-2.5 w-full rounded-full bg-[#D9DEE3] dark:bg-gray-700">
                        <div class="h-2.5 rounded-full bg-[#E15B3F]" style="width: {{ $ticket['percentage'] }}%"></div>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($ticket['percentage'], 1) }}% terjual</span>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Rp {{ number_format($ticket['revenue'], 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Detailed Ticket Sales Table -->
    <div class="mb-6 overflow-hidden rounded-2xl border border-[#D9DEE3] bg-white shadow-sm dark:bg-gray-800">
        <div class="p-4 border-b dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Detail Penjualan Tiket</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis Tiket</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Harga</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tersedia</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Terjual</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pendapatan</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Persentase</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($event->tickets as $ticket)
                        @php
                            $sold = $ticket->orders->sum('quantity');
                            $revenue = $ticket->price * $sold;
                            $percentage = $ticket->quota_avail > 0 ? ($sold / $ticket->quota_avail) * 100 : 0;
                        @endphp
                        <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $ticket->ticket_class }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($ticket->description, 30) }}</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">Rp {{ number_format($ticket->price, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $ticket->quota_avail }}</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $sold }}</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                    <div class="bg-indigo-600 dark:bg-indigo-500 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <div class="text-xs mt-1 text-gray-500 dark:text-gray-400">{{ number_format($percentage, 1) }}%</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@else
    <!-- No Event Selected -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
        <div class="text-gray-500 dark:text-gray-400">
            <i class="fas fa-chart-line text-4xl mb-3"></i>
            <p class="text-lg mb-4">Pilih acara untuk melihat analitik</p>
            <p class="text-sm">Anda dapat melihat detail penjualan tiket, pendapatan, dan statistik untuk acara tertentu.</p>
        </div>
    </div>
@endif

@push('scripts')
<script>
    function selectEvent(eventId) {
        if (eventId) {
            window.location.href = "{{ route('admin.analytics') }}/" + eventId;
        } else {
            window.location.href = "{{ route('admin.analytics') }}";
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if(isset($event) && isset($chartData))
        // Debug output to console
        console.log("Chart data available:", @json($chartData));

        // Initialize chart
        var options = {
            chart: {
                type: 'bar',
                height: 350,
                stacked: false,
                toolbar: {
                    show: true
                },
                foreColor: document.querySelector('html').classList.contains('dark') ? '#e5e7eb' : '#374151'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            series: [{
                name: 'Tiket Terjual',
                data: @json($chartData['sold'])
            }],
            xaxis: {
                categories: @json($chartData['labels']),
            },
            yaxis: [
                {
                    title: {
                        text: 'Jumlah Tiket',
                    },
                }
            ],
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " tiket"
                    }
                }
            },
            colors: ['#4f46e5']
        };

        var chart = new ApexCharts(document.querySelector("#sales-chart"), options);
        chart.render();
        @else
        console.log("Chart data not available");
        @endif
    });
</script>
@endpush
@endsection
