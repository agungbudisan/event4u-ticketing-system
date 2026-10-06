<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Payment;
use App\Models\Category;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\AnalyticsExport;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        // Auto-mark payment pending yang sudah lewat batas waktu sebagai expired (termasuk order guest)
        Payment::expireOverdue();

        // Statistik dasar
        $totalEvents = Event::count();
        $upcomingEvents = Event::where('start_event', '>', now())->count();
        $categories = Category::count();
        $activeTickets = Ticket::sum('quota_avail');

        // Acara terbaru
        $recentEvents = Event::with('category')
            ->latest()
            ->take(5)
            ->get();

        // Pesanan terbaru
        $recentOrders = Order::with(['ticket.event', 'payment', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Distribusi kategori untuk pie chart
        $categoryData = Category::withCount('events')->get();
        $categoryNames = $categoryData->pluck('name')->toArray();
        $eventCounts = $categoryData->pluck('events_count')->toArray();

        // Data penjualan bulanan untuk bar chart
        $monthlySalesLabels = [];
        $monthlySalesData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlySalesLabels[] = $month->format('M Y');

            $salesCount = Order::whereHas('payment', function($query) {
                    $query->whereIn('status', ['pending', 'completed']);
                })
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('quantity');

            $monthlySalesData[] = $salesCount;
        }

        return view('admin.dashboard', array_merge(compact(
            'totalEvents',
            'upcomingEvents',
            'categories',
            'activeTickets',
            'recentEvents',
            'recentOrders',
            'categoryNames',
            'eventCounts',
            'monthlySalesLabels',
            'monthlySalesData'
        ), [
            'title' => 'Dashboard Admin',
            'header' => 'Dashboard Utama'
        ]));
    }

    public function analytics()
    {
        $eventId = request()->route('eventId');

        // Data dasar yang selalu dikirim
        $viewData = [
            'title' => 'Analitik Acara',
            'header' => 'Analitik Acara',
            'events' => Event::with(['tickets.orders', 'category'])->get()
        ];

        if ($eventId) {
            $event = Event::with(['tickets.orders', 'category'])->findOrFail($eventId);

            // Hitung total penjualan dan tiket terjual
            $totalSales = 0;
            $totalTicketsSold = 0;
            $ticketTypes = [];

            foreach ($event->tickets as $ticket) {
                $sold = $ticket->orders->sum('quantity');
                $revenue = $ticket->price * $sold;

                $totalSales += $revenue;
                $totalTicketsSold += $sold;

                $ticketTypes[] = [
                    'name' => $ticket->ticket_class,
                    'sold' => $sold,
                    'revenue' => $revenue,
                    'quota' => $ticket->quota_avail,
                    'percentage' => $ticket->quota_avail > 0 ? ($sold / $ticket->quota_avail) * 100 : 0
                ];
            }

            // Data untuk chart
            $chartData = [
                'labels' => collect($ticketTypes)->pluck('name'),
                'sold' => collect($ticketTypes)->pluck('sold'),
                'revenue' => collect($ticketTypes)->pluck('revenue')
            ];

            // Gabungkan dengan data tambahan
            $viewData = array_merge($viewData, [
                'event' => $event,
                'totalSales' => $totalSales,
                'totalTicketsSold' => $totalTicketsSold,
                'ticketTypes' => $ticketTypes,
                'chartData' => $chartData
            ]);
        }

        return view('admin.analytics', $viewData);
    }

    // Export to Excel
    public function exportExcel($eventId = null)
    {
        // Ambil data yang diperlukan untuk analitik, sama seperti di method analytics()
        $data = $this->getAnalyticsData($eventId);

        // Jika data kosong (tidak ada event yang dipilih), gunakan nama file default
        if (empty($data)) {
            return Excel::download(new AnalyticsExport(null, 0, 0, []), 'all-events-analytics-'.now()->format('Y-m-d').'.xlsx');
        }

        // Buat nama file yang bersih (tanpa karakter yang tidak diperbolehkan dalam nama file)
        $eventName = $data['event']->title;
        $safeEventName = Str::slug($eventName);
        $fileName = 'analytics-'.$safeEventName.'-'.$eventId.'-'.now()->format('Y-m-d').'.xlsx';

        // Pastikan data ada dan valid
        return Excel::download(new AnalyticsExport(
            $data['event'],             // Event
            $data['totalSales'],        // Total Sales
            $data['totalTicketsSold'],  // Total Tickets Sold
            $data['ticketTypes']        // Ticket Types
        ), $fileName);
    }

    // Export to PDF
    public function exportPdf($eventId = null)
    {
        // Ambil data yang diperlukan untuk analitik, sama seperti di method analytics()
        $data = $this->getAnalyticsData($eventId);

        // Jika data kosong (tidak ada event yang dipilih), gunakan nama file default
        if (empty($data)) {
            $pdf = Pdf::loadView('admin.analytics_pdf', ['event' => null]);
            return $pdf->download('all-events-analytics-'.now()->format('Y-m-d').'.pdf');
        }

        // Buat nama file yang bersih (tanpa karakter yang tidak diperbolehkan dalam nama file)
        $eventName = $data['event']->title;
        $safeEventName = Str::slug($eventName);
        $fileName = 'analytics-'.$safeEventName.'-'.$eventId.'-'.now()->format('Y-m-d').'.pdf';

        // Menggunakan DomPDF untuk render PDF
        $pdf = Pdf::loadView('admin.analytics_pdf', $data);

        return $pdf->download($fileName);
    }

    protected function getAnalyticsData($eventId)
    {
        if ($eventId) {
            $event = Event::with(['tickets.orders', 'category'])->findOrFail($eventId);

            $ticketTypes = [];
            $totalSales = 0;
            $totalTicketsSold = 0;

            foreach ($event->tickets as $ticket) {
                $sold = $ticket->orders->sum('quantity');
                $revenue = $ticket->price * $sold;

                $totalSales += $revenue;
                $totalTicketsSold += $sold;

                $ticketTypes[] = [
                    'name' => $ticket->ticket_class,
                    'sold' => $sold,
                    'revenue' => $revenue,
                    'quota' => $ticket->quota_avail,
                    'percentage' => $ticket->quota_avail > 0 ? ($sold / $ticket->quota_avail) * 100 : 0
                ];
            }

            return [
                'event' => $event,
                'totalSales' => $totalSales,
                'totalTicketsSold' => $totalTicketsSold,
                'ticketTypes' => $ticketTypes
            ];
        }

        return []; // Return empty array if no event ID
    }
}
