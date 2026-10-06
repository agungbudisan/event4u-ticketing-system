<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Ticket;
use App\Models\Order;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store', 'show', 'index', 'downloadETicketPdf']);
        $this->middleware('admin')->only(['adminIndex', 'adminShow']);
    }

    /**
     * Display a listing of orders for authenticated user
     */
    public function index(Request $request)
    {
        // Auto-mark payment pending yang sudah lewat batas waktu sebagai expired
        \App\Models\Payment::expirePendingForUser(Auth::id());

        $query = Order::where('user_id', Auth::id())
            ->with(['ticket.event', 'payment']);

        // Search by event title
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('ticket.event', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        // Filter by payment status
        if ($request->has('status') && !empty($request->status)) {
            $query->whereHas('payment', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('total_price', 'desc');
                    break;
                case 'price_low':
                    $query->orderBy('total_price', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    /**
     * Display a listing of all orders for admin
     */
    public function adminIndex(Request $request)
    {
        // Auto-mark payment pending yang sudah lewat batas waktu sebagai expired (termasuk order guest)
        \App\Models\Payment::expireOverdue();

        $query = Order::with(['ticket.event', 'payment', 'user']);

        // Filter berdasarkan search query
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('ticket.event', function($q2) use ($search) {
                      $q2->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan status pembayaran
        if ($request->has('status') && !empty($request->status)) {
            $query->whereHas('payment', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Filter berdasarkan event
        if ($request->has('event_id') && !empty($request->event_id)) {
            $eventId = $request->event_id;
            $query->whereHas('ticket', function($q) use ($eventId) {
                $q->where('event_id', $eventId);
            });
        }

        // Filter berdasarkan rentang tanggal
        if ($request->has('date_from') && !empty($request->date_from)) {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $query->where('order_date', '>=', $dateFrom);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $dateTo = Carbon::parse($request->date_to)->endOfDay();
            $query->where('order_date', '<=', $dateTo);
        }

        // Pengurutan
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('order_date', 'asc');
                break;
            case 'price_high':
                $query->orderBy('total_price', 'desc');
                break;
            case 'price_low':
                $query->orderBy('total_price', 'asc');
                break;
            default:
                $query->orderBy('order_date', 'desc');
                break;
        }

        $orders = $query->paginate(15)->withQueryString();

        // Hitung total pendapatan dari orders yang sudah completed payment
        $totalRevenue = Order::whereHas('payment', function($q) {
            $q->where('status', 'completed');
        })->sum('total_price');

        $completedOrders = Order::whereHas('payment', function($q) {
            $q->where('status', 'completed');
        })->count();

        // Ambil semua event untuk dropdown filter
        $events = Event::orderBy('title')->get();

        return view('admin.orders.index', compact('orders', 'totalRevenue', 'completedOrders', 'events'));
    }

    /**
     * Show the form for creating a new order for authenticated user
     */
    public function create(Ticket $ticket, Request $request)
    {
        // Gunakan single query parameter 'qty' untuk jumlah tiket
        $quantity = $request->query('qty', 1);

        // Konversi ke integer
        $quantity = (int) $quantity;

        // Validasi quantity
        if ($quantity < 1 || $quantity > 5) {
            $quantity = 1; // Default jika invalid
        }

        // Pastikan tidak melebihi quota_avail
        $quantity = min($quantity, $ticket->quota_avail);

        return view('orders.create', compact('ticket', 'quantity'));
    }

    /**
     * Store a newly created order for authenticated user
     */
    public function store(Request $request, Ticket $ticket)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'email' => 'required|email',
        ]);

        $totalPrice = $ticket->price * $request->quantity;

        $order = Order::create([
            'total_price' => $totalPrice,
            'quantity' => $request->quantity,
            'email' => $request->email,
            'order_date' => now(),
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('payments.create', $order)->with('success', 'Order placed successfully. Proceed to payment.');
    }

    /**
     * Show the form for creating a new order for guest user
     */
    public function guestCreate(Request $request, Ticket $ticket)
    {
        // Gunakan single query parameter 'qty' untuk jumlah tiket
        $quantity = $request->query('qty', 1);

        // Konversi ke integer
        $quantity = (int) $quantity;

        // Validasi quantity
        if ($quantity < 1 || $quantity > 5) {
            $quantity = 1; // Default jika invalid
        }

        // Pastikan tidak melebihi quota_avail
        $quantity = min($quantity, $ticket->quota_avail);

        return view('guest.orders.create', compact('ticket', 'quantity'));
    }

    /**
     * Store a newly created order for guest user
     */
    public function guestStore(Request $request, Ticket $ticket)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        $totalPrice = $ticket->price * $request->quantity;
        $reference = 'ORD-' . Str::upper(Str::random(8));

        $order = Order::create([
            'total_price' => $totalPrice,
            'quantity' => $request->quantity,
            'email' => $request->email,
            'order_date' => now(),
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'guest_name' => $request->name,
            'guest_phone' => $request->phone,
            'reference' => $reference,
        ]);

        // Store order reference in session for guest users
        session(['guest_order_reference' => $reference]);

        return redirect()->route('guest.payments.midtrans', $reference)
            ->with('success', 'Order placed successfully. Proceed to payment.');
    }

    /**
     * Display the specified order for authenticated user
     */
    public function show(Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Auto-mark payment pending yang sudah lewat batas waktu sebagai expired
        if ($order->payment && $order->payment->status === 'pending') {
            $paymentExpired = $order->payment->expires_at && now()->isAfter($order->payment->expires_at);
            $orderExpired = $order->expires_at && now()->isAfter($order->expires_at);

            if ($paymentExpired || $orderExpired) {
                $order->payment->status = 'expired';
                $order->payment->save();
            }
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Display the specified order for admin
     */
    public function adminShow(Order $order)
    {
        $order->load(['ticket.event', 'payment', 'user']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Display order confirmation for guest user
     */
    public function guestConfirmation($reference)
    {
        $order = Order::where('reference', $reference)
            ->with(['ticket.event', 'payment'])
            ->firstOrFail();

        return view('guest.orders.confirmation', compact('order'));
    }

    /**
     * Download e-ticket PDF for authenticated user
     */
    public function downloadETicketPdf(Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Pastikan order punya payment dengan status completed
        if (!$order->payment || $order->payment->status !== 'completed') {
            return back()->with('error', 'E-ticket hanya tersedia setelah pembayaran dikonfirmasi oleh admin.');
        }

        // Generate QR Code sebagai SVG Base64
        $qrData = [
            'order_id' => $order->id,
            'event' => $order->ticket->event->title,
            'ticket_class' => $order->ticket->ticket_class,
            'quantity' => $order->quantity,
            'attendee' => $order->user->name,
            'email' => $order->email
        ];

        $jsonData = json_encode($qrData);

        // Generate QR Code sebagai SVG (tidak memerlukan Imagick)
        $svgQrCode = QrCode::format('svg')
            ->size(200)
            ->margin(1)
            ->generate($jsonData);

        // Convert SVG ke Base64
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgQrCode);

        // Load PDF view dengan mengirimkan QR Code Base64
        $pdf = Pdf::loadView('pdfs.eticket', [
            'order' => $order,
            'qrCodeBase64' => $qrCodeBase64
        ]);

        return $pdf->download('e-ticket-' . $order->id . '.pdf');
    }

    /**
     * Download e-ticket PDF for guest user
     */
    public function downloadETicketPdfGuest($reference)
    {
        $order = Order::where('reference', $reference)
            ->with(['ticket.event', 'payment'])
            ->firstOrFail();

        // Pastikan order punya payment dengan status completed
        if (!$order->payment || $order->payment->status !== 'completed') {
            return redirect()->route('guest.orders.confirmation', $reference)
                ->with('error', 'E-ticket hanya tersedia setelah pembayaran dikonfirmasi oleh admin.');
        }

        // Generate QR Code sebagai SVG Base64
        $qrData = [
            'reference' => $order->reference,
            'event' => $order->ticket->event->title,
            'ticket_class' => $order->ticket->ticket_class,
            'quantity' => $order->quantity,
            'attendee' => $order->guest_name,
            'email' => $order->email
        ];

        $jsonData = json_encode($qrData);

        // Generate QR Code sebagai SVG (tidak memerlukan Imagick)
        $svgQrCode = QrCode::format('svg')
            ->size(200)
            ->margin(1)
            ->generate($jsonData);

        // Convert SVG ke Base64
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgQrCode);

        // Load PDF view dengan mengirimkan QR Code Base64
        $pdf = Pdf::loadView('pdfs.guest-eticket', [
            'order' => $order,
            'qrCodeBase64' => $qrCodeBase64
        ]);

        return $pdf->download('e-ticket-' . $reference . '.pdf');
    }
}
