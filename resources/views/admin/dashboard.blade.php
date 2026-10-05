@extends('admin.layouts.app')

@section('content')
<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Events Card -->
    <div class="rounded-2xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#7B0015]">
                <i class="fas fa-calendar-alt text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-semibold text-[#52606D] dark:text-gray-400">Total Acara</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalEvents }}</p>
            </div>
        </div>
    </div>

    <!-- Upcoming Events Card -->
    <div class="rounded-2xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#E15B3F]">
                <i class="fas fa-hourglass-half text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-semibold text-[#52606D] dark:text-gray-400">Acara Mendatang</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $upcomingEvents }}</p>
            </div>
        </div>
    </div>

    <!-- Categories Card -->
    <div class="rounded-2xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#D1A83A]">
                <i class="fas fa-tags text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-semibold text-[#52606D] dark:text-gray-400">Kategori</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $categories }}</p>
            </div>
        </div>
    </div>

    <!-- Active Tickets Card -->
    <div class="rounded-2xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#B7372B]">
                <i class="fas fa-ticket-alt text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-semibold text-[#52606D] dark:text-gray-400">Tiket Aktif</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $activeTickets }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Events Table -->
<div class="mb-6 overflow-hidden rounded-2xl border border-[#D9DEE3] bg-white shadow-sm dark:bg-gray-800">
    <div class="p-4 flex justify-between items-center border-b dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Acara Terbaru</h2>
        <a href="{{ route('admin.events.index') }}" class="text-sm font-semibold text-[#7B0015] hover:text-[#E15B3F]">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
            <thead class="bg-[#EEF1F4] dark:bg-gray-700">
                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Judul</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kategori</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Lokasi</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal Mulai</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($recentEvents as $event)
                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $event->title }}</div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $event->category->name }}</div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $event->location }}</div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $event->start_event->format('d M Y, H:i') }}</div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.events.show', $event) }}" class="mr-3 font-semibold text-[#7B0015] hover:text-[#E15B3F]">Detail</a>
                        <a href="{{ route('admin.events.edit', $event) }}" class="text-yellow-600 dark:text-yellow-500 hover:text-yellow-900 dark:hover:text-yellow-400">Edit</a>
                    </td>
                </tr>
                @empty
                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                    <td colspan="5" class="px-6 py-5 text-center text-sm text-gray-500 dark:text-gray-400">
                        Tidak ada acara terbaru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Orders Table -->
@if(isset($recentOrders))
<div class="mb-6 overflow-hidden rounded-2xl border border-[#D9DEE3] bg-white shadow-sm dark:bg-gray-800">
    <div class="p-4 flex justify-between items-center border-b dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pesanan Terbaru</h2>
        <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-[#7B0015] hover:text-[#E15B3F]">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
            <thead class="bg-[#EEF1F4] dark:bg-gray-700">
                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acara</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tiket</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pembeli</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($recentOrders as $order)
                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->ticket->event->title }}</div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->ticket->name ?? $order->ticket->ticket_class }}</div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $order->uid ? ($order->user->name ?? 'User #'.$order->uid) : ($order->guest_name ?? $order->email) }}
                        </div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->quantity }}</div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        @php
                            $statusClass = 'gray';
                            $statusText = $order->payment ? $order->payment->status_label : 'Belum Dibayar';

                            if(isset($order->payment)) {
                                if($order->payment->status == 'pending') {
                                    $statusClass = 'yellow';
                                } elseif($order->payment->status == 'completed') {
                                    $statusClass = 'green';
                                } elseif($order->payment->status == 'failed') {
                                    $statusClass = 'red';
                                } elseif($order->payment->status == 'cancelled') {
                                    $statusClass = 'red';
                                } elseif($order->payment->status == 'expired') {
                                    $statusClass = 'gray';
                                }
                            }
                        @endphp
                        @php
                            $statusStyles = [
                                'gray' => 'bg-[#E2E8F0] text-[#334155]',
                                'yellow' => 'bg-[#FEF3C7] text-[#92400E]',
                                'green' => 'bg-[#DCFCE7] text-[#166534]',
                                'red' => 'bg-[#FEE2E2] text-[#991B1B]',
                            ];
                        @endphp
                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $statusStyles[$statusClass] ?? $statusStyles['gray'] }}">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-[#7B0015] hover:text-[#E15B3F]">Detail</a>
                    </td>
                </tr>
                @empty
                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                    <td colspan="7" class="px-6 py-5 text-center text-sm text-gray-500 dark:text-gray-400">
                        Tidak ada pesanan terbaru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Category Distribution Chart -->
    <div class="rounded-2xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Distribusi Kategori Acara</h2>
        <div id="category-chart" class="h-80"></div>
    </div>

    <!-- Monthly Sales Chart -->
    @if(isset($monthlySalesLabels) && isset($monthlySalesData))
    <div class="rounded-2xl border border-[#D9DEE3] bg-white p-4 shadow-sm dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Penjualan Tiket Bulanan</h2>
        <div id="sales-chart" class="h-80"></div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Category Distribution Chart
        const categoryNames = @json($categoryNames ?? []);
        const eventCounts = @json($eventCounts ?? []);

        if (categoryNames.length > 0 && eventCounts.length > 0) {
            var categoryOptions = {
                chart: {
                    type: 'pie',
                    height: 350,
                    foreColor: document.querySelector('html').classList.contains('dark') ? '#e5e7eb' : '#374151'
                },
                series: eventCounts,
                labels: categoryNames,
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                colors: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#F97316', '#14B8A6', '#6366F1'],
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value + ' acara';
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '14px'
                }
            };

            var categoryChart = new ApexCharts(document.querySelector("#category-chart"), categoryOptions);
            categoryChart.render();
        } else {
            document.querySelector("#category-chart").innerHTML = '<div class="flex items-center justify-center h-full"><p class="text-gray-500 dark:text-gray-400">Tidak ada data kategori untuk ditampilkan.</p></div>';
        }

        // Monthly Sales Chart
        @if(isset($monthlySalesLabels) && isset($monthlySalesData))
        const monthlySalesLabels = @json($monthlySalesLabels);
        const monthlySalesData = @json($monthlySalesData);

        var salesOptions = {
            chart: {
                type: 'bar',
                height: 350,
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
                name: 'Penjualan Tiket',
                data: monthlySalesData
            }],
            xaxis: {
                categories: monthlySalesLabels,
            },
            yaxis: {
                title: {
                    text: 'Jumlah Tiket Terjual'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " tiket"
                    }
                }
            },
            colors: ['#4F46E5']
        };

        var salesChart = new ApexCharts(document.querySelector("#sales-chart"), salesOptions);
        salesChart.render();
        @endif
    });
</script>
@endpush
@endsection
