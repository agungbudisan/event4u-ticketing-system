<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Models\Event;
use App\Mail\SendETicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store', 'index', 'show', 'processMidtransPayment', 'finishMidtransPayment', 'checkOrderStatus']);
        $this->middleware('admin')->only(['adminIndex', 'adminShow', 'updateStatus']);
    }

    /**
     * Display a listing of all payments for admin
     */
    public function adminIndex(Request $request)
    {
        // Auto-mark payment pending yang sudah lewat batas waktu sebagai expired (termasuk order guest)
        \App\Models\Payment::expireOverdue();

        $query = Payment::with(['order.ticket.event', 'order.user']);

        // Filter berdasarkan search query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('order', function($q2) use ($search) {
                      $q2->where('reference', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order.user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order.ticket.event', function($q2) use ($search) {
                      $q2->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan status pembayaran
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan metode pembayaran
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Filter berdasarkan event
        if ($request->filled('event_id')) {
            $eventId = $request->event_id;
            $query->whereHas('order.ticket', function($q) use ($eventId) {
                $q->where('event_id', $eventId);
            });
        }

        // Filter berdasarkan rentang tanggal
        if ($request->filled('date_from')) {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($request->filled('date_to')) {
            $dateTo = Carbon::parse($request->date_to)->endOfDay();
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Pengurutan
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'amount_high':
                $query->orderByDesc(function($query) {
                    $query->select('total_price')
                        ->from('orders')
                        ->whereColumn('orders.id', 'payments.order_id');
                });
                break;
            case 'amount_low':
                $query->orderBy(function($query) {
                    $query->select('total_price')
                        ->from('orders')
                        ->whereColumn('orders.id', 'payments.order_id');
                });
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $payments = $query->paginate(15)->withQueryString();

        // Get data untuk dropdown filter
        $events = Event::orderBy('title')->get();

        // Statistik pembayaran
        $totalCompleted = Payment::where('status', 'completed')->count();
        $totalPending = Payment::where('status', 'pending')->count();
        $totalRevenue = Order::whereHas('payment', function($q) {
            $q->where('status', 'completed');
        })->sum('total_price');

        return view('admin.payments.index', compact('payments', 'events', 'totalCompleted', 'totalPending', 'totalRevenue'));
    }

    /**
     * Display a specified payment for admin
     */
    public function adminShow(Payment $payment)
    {
        $payment->load(['order.ticket.event', 'order.user']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Display the payment form for authenticated users
     */
    public function create(Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the order is expired
        if ($order->isExpired()) {
            return redirect()->route('orders.index')
                ->with('error', 'Batas waktu pembayaran telah habis. Silakan buat pesanan baru.');
        }

        // Check if payment already exists and is completed
        if ($order->payment && $order->payment->status === 'completed') {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Pembayaran untuk pesanan ini sudah selesai.');
        }

        // Langsung arahkan ke pembayaran Midtrans
        return redirect()->route('payments.midtrans', $order);
    }

    /**
     * Store a payment for authenticated users
     */
    public function store(Request $request, Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the order is expired
        if ($order->isExpired()) {
            return redirect()->route('orders.index')
                ->with('error', 'Batas waktu pembayaran telah habis. Silakan buat pesanan baru.');
        }

        // Check if payment already exists
        if ($order->payment) {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Pembayaran untuk pesanan ini sudah dilakukan sebelumnya.');
        }

        // Langsung arahkan ke pembayaran Midtrans
        return $this->processMidtransPayment($order);
    }

    /**
     * Display the payment form for guest users
     */
    public function guestCreate($reference)
    {
        $order = Order::where('reference', $reference)
            ->with('ticket.event')
            ->firstOrFail();

        // Cek apakah order sudah memiliki payment yang completed
        if ($order->payment && $order->payment->status === 'completed') {
            return redirect()->route('guest.orders.confirmation', $reference)
                ->with('info', 'Pembayaran untuk pesanan ini sudah selesai.');
        }

        // Cek apakah order sudah melewati batas waktu
        if ($order->isExpired()) {
            return redirect()->route('welcome')
                ->with('error', 'Batas waktu pembayaran telah berakhir.');
        }

        return view('guest.payments.midtrans', compact('order'));
    }

    /**
     * Store a payment for guest users
     */
    public function guestStore(Request $request, $reference)
    {
        $order = Order::where('reference', $reference)->firstOrFail();

        // Check if the reference matches the one in session
        if (!session('guest_order_reference') || session('guest_order_reference') !== $reference) {
            abort(403, 'Unauthorized action.');
        }

        // Check if payment already exists
        if ($order->payment) {
            return redirect()->route('guest.orders.confirmation', $reference)
                ->with('info', 'Pembayaran untuk pesanan ini sudah dilakukan sebelumnya.');
        }

        // Langsung arahkan ke pembayaran Midtrans
        return redirect()->route('guest.payments.midtrans', $reference);
    }

    /**
     * Display user's payments
     */
    public function index()
    {
        $payments = Payment::whereHas('order', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->with(['order.ticket.event'])
            ->latest()
            ->paginate(10);

        return view('payments.index', compact('payments'));
    }

    /**
     * Memproses pembayaran dengan Midtrans untuk user terotentikasi
     */
    public function processMidtransPayment(Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the order is expired
        if ($order->isExpired()) {
            return redirect()->route('orders.index')
                ->with('error', 'Batas waktu pembayaran telah habis. Silakan buat pesanan baru.');
        }

        // Check if payment already exists and is completed
        if ($order->payment && $order->payment->status === 'completed') {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Pembayaran untuk pesanan ini sudah selesai.');
        }

        // Jika payment sudah ada tapi belum completed, gunakan payment yang ada
        // Ini mencegah duplikasi record pembayaran untuk pesanan yang sama
        $payment = $order->payment;
        $isExistingPayment = false;

        if ($payment && $payment->status === 'pending' && $payment->snap_token) {
            $isExistingPayment = true;
            Log::info('Using existing payment record with snap token: ' . $payment->snap_token);
        } else {
            try {
                // Setting Midtrans configuration
                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = config('midtrans.is_production');
                \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
                \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

                // Buat ID transaksi unik
                $transactionId = 'ORDER-' . $order->id . '-' . Str::random(8);

                // Set waktu kedaluwarsa default
                $defaultExpiryDuration = config('midtrans.expiry_durations.default', 60);
                $expiryTime = now()->addMinutes($defaultExpiryDuration);

                // Set parameter untuk Midtrans
                $params = [
                    'transaction_details' => [
                        'order_id' => $transactionId,
                        'gross_amount' => (int) $order->total_price,
                    ],
                    'customer_details' => [
                        'first_name' => $order->user->name,
                        'email' => $order->email,
                    ],
                    'item_details' => [
                        [
                            'id' => $order->ticket->id,
                            'price' => (int) $order->ticket->price,
                            'quantity' => $order->quantity,
                            'name' => $order->ticket->event->title . ' - ' . $order->ticket->ticket_class,
                        ],
                    ],
                    'expiry' => [
                        'unit' => 'minute',
                        'duration' => $defaultExpiryDuration
                    ],
                    'callbacks' => [
                        'finish' => route('payments.midtrans.finish', $order->id),
                    ],
                ];

                // Tambahkan URL notifikasi jika dikonfigurasi
                if (config('midtrans.append_notif_url', true)) {
                    $params['callbacks']['notification'] = route('payments.midtrans.notification');
                }

                // Dapatkan Snap Token
                $snapToken = \Midtrans\Snap::getSnapToken($params);

                // Buat atau update payment record
                if (!$payment) {
                    $payment = new Payment();
                    $payment->order_id = $order->id;
                }

                $payment->method = 'midtrans';
                $payment->status = 'pending';
                $payment->transaction_id = $transactionId;
                $payment->snap_token = $snapToken;
                $payment->payment_date = now();
                $payment->expires_at = $expiryTime;
                $payment->save();

                // Update order expiry time
                $order->expires_at = $expiryTime;
                $order->save();

                Log::info('New payment record created with snap token: ' . $snapToken);

            } catch (\Exception $e) {
                Log::error('Midtrans Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

        // Return view dengan snap token
        return view('payments.midtrans', [
            'order' => $order,
            'snap_token' => $payment->snap_token,
            'expires_at' => $payment->expires_at->timestamp * 1000 // Untuk JS countdown
        ]);
    }

    /**
     * Memproses pembayaran dengan Midtrans untuk guest user
     */
    public function processMidtransPaymentGuest($reference)
    {
        $order = Order::where('reference', $reference)
            ->with('ticket.event')
            ->firstOrFail();

        // Verifikasi referensi di session
        if (!session('guest_order_reference') || session('guest_order_reference') !== $reference) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah order sudah kedaluwarsa
        if ($order->isExpired()) {
            return redirect()->route('welcome')
                ->with('error', 'Batas waktu pembayaran telah berakhir.');
        }

        // Cek apakah sudah ada pembayaran
        if ($order->payment && $order->payment->status === 'completed') {
            return redirect()->route('guest.orders.confirmation', $reference)
                ->with('info', 'Pembayaran untuk pesanan ini sudah selesai.');
        }

        // Gunakan pembayaran yang ada jika belum selesai
        $payment = $order->payment;
        $isExistingPayment = false;

        if ($payment && $payment->status === 'pending' && $payment->snap_token) {
            $isExistingPayment = true;
            Log::info('Using existing guest payment record with snap token: ' . $payment->snap_token);
        } else {
            try {
                // Setting Midtrans configuration
                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = config('midtrans.is_production');
                \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
                \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

                // Buat ID transaksi unik
                $transactionId = 'GUEST-' . $reference . '-' . Str::random(8);

                // Set waktu kedaluwarsa default
                $defaultExpiryDuration = config('midtrans.expiry_durations.default', 60);
                $expiryTime = now()->addMinutes($defaultExpiryDuration);

                // Set parameter untuk Midtrans
                $params = [
                    'transaction_details' => [
                        'order_id' => $transactionId,
                        'gross_amount' => (int) $order->total_price,
                    ],
                    'customer_details' => [
                        'first_name' => $order->guest_name,
                        'email' => $order->email,
                        'phone' => $order->guest_phone,
                    ],
                    'item_details' => [
                        [
                            'id' => $order->ticket->id,
                            'price' => (int) $order->ticket->price,
                            'quantity' => $order->quantity,
                            'name' => $order->ticket->event->title . ' - ' . $order->ticket->ticket_class,
                        ],
                    ],
                    'expiry' => [
                        'unit' => 'minute',
                        'duration' => $defaultExpiryDuration
                    ],
                    'callbacks' => [
                        'finish' => route('guest.orders.confirmation', $reference),
                    ],
                ];

                // Tambahkan URL notifikasi jika dikonfigurasi
                if (config('midtrans.append_notif_url', true)) {
                    $params['callbacks']['notification'] = route('payments.midtrans.notification');
                }

                // Dapatkan Snap Token
                $snapToken = \Midtrans\Snap::getSnapToken($params);

                // Buat payment record
                $payment = new Payment([
                    'method' => 'midtrans',
                    'status' => 'pending',
                    'transaction_id' => $transactionId,
                    'snap_token' => $snapToken,
                    'payment_date' => now(),
                    'expires_at' => $expiryTime,
                    'order_id' => $order->id,
                    'guest_email' => $order->email,
                ]);
                $payment->save();

                // Update order expiry time
                $order->expires_at = $expiryTime;
                $order->save();

                Log::info('New guest payment record created with snap token: ' . $snapToken);

            } catch (\Exception $e) {
                Log::error('Midtrans Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

        // Return view dengan snap token
        return view('guest.payments.midtrans', [
            'order' => $order,
            'snap_token' => $payment->snap_token,
            'expires_at' => $payment->expires_at->timestamp * 1000 // Untuk JS countdown
        ]);
    }

    /**
     * Handle callback setelah pembayaran selesai (untuk User)
     */
    public function finishMidtransPayment(Order $order, Request $request)
    {
        // Verifikasi hak akses
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Log semua data yang diterima dari Midtrans untuk debugging
        Log::info('Midtrans finish callback data (Member): ' . json_encode($request->all()));

        // Cek apakah ada data transaksi dari Midtrans
        if ($request->has('transaction_status') && $request->has('order_id')) {
            // Cari payment berdasarkan transaction_id
            $payment = Payment::where('transaction_id', $request->order_id)
                ->where('order_id', $order->id)
                ->first();

            if ($payment) {
                // Update status payment berdasarkan status transaksi
                $transactionStatus = $request->transaction_status;

                if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                    $payment->status = 'completed';
                    $payment->save();

                    // Proses e-ticket
                    $order->ticket->decrement('quota_avail', $order->quantity);

                    try {
                        $order = $order->fresh(['ticket.event', 'user']);
                        Mail::to($order->email)->send(new SendETicket($order, false));
                        Log::info('E-ticket sent to registered user: ' . $order->email);
                    } catch (\Exception $e) {
                        Log::error('Failed to send e-ticket: ' . $e->getMessage());
                    }

                    return redirect()->route('orders.show', $order)
                        ->with('success', 'Pembayaran berhasil! E-ticket telah dikirim ke email Anda.');
                } else if ($transactionStatus == 'pending') {
                    $payment->status = 'pending';
                    $payment->save();

                    return redirect()->route('orders.show', $order)
                        ->with('info', 'Pembayaran sedang dalam proses. Kami akan mengirim e-ticket setelah pembayaran dikonfirmasi.');
                } else if ($transactionStatus == 'expire') {
                    $payment->status = 'expired';
                    $payment->save();

                    return redirect()->route('orders.show', $order)
                        ->with('error', 'Pembayaran telah kedaluwarsa. Silakan buat pesanan baru.');
                } else {
                    $payment->status = 'failed';
                    $payment->save();

                    return redirect()->route('orders.show', $order)
                        ->with('error', 'Pembayaran gagal atau dibatalkan.');
                }
            } else {
                Log::error('Payment record not found for transaction_id: ' . $request->order_id);
            }
        } else {
            Log::warning('Missing transaction data in Midtrans finish callback');
        }

        return redirect()->route('orders.show', $order)
            ->with('info', 'Status pembayaran akan diperbarui segera.');
    }

    /**
     * Handle callback setelah pembayaran selesai (untuk Guest)
     */
    public function finishMidtransPaymentGuest($reference, Request $request)
    {
        $order = Order::where('reference', $reference)->firstOrFail();

        // Log semua data yang diterima dari Midtrans untuk debugging
        Log::info('Midtrans finish callback data (Guest): ' . json_encode($request->all()));

        // Cek apakah ada data transaksi dari Midtrans
        if ($request->has('transaction_status') && $request->has('order_id')) {
            // Cari payment berdasarkan transaction_id
            $payment = Payment::where('transaction_id', $request->order_id)
                ->where('order_id', $order->id)
                ->first();

            if ($payment) {
                // Update status payment berdasarkan status transaksi
                $transactionStatus = $request->transaction_status;

                if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                    $payment->status = 'completed';
                    $payment->save();

                    // Proses e-ticket
                    $order->ticket->decrement('quota_avail', $order->quantity);

                    try {
                        $order = $order->fresh(['ticket.event']);
                        Mail::to($order->email)->send(new SendETicket($order, true));
                        Log::info('E-ticket sent to guest: ' . $order->email);
                    } catch (\Exception $e) {
                        Log::error('Failed to send e-ticket: ' . $e->getMessage());
                    }

                    // Hapus referensi dari session
                    session()->forget('guest_order_reference');

                    return redirect()->route('guest.orders.confirmation', $reference)
                        ->with('success', 'Pembayaran berhasil! E-ticket telah dikirim ke email Anda.');
                } else if ($transactionStatus == 'pending') {
                    $payment->status = 'pending';
                    $payment->save();

                    return redirect()->route('guest.orders.confirmation', $reference)
                        ->with('info', 'Pembayaran sedang dalam proses. Kami akan mengirim e-ticket setelah pembayaran dikonfirmasi.');
                } else if ($transactionStatus == 'expire') {
                    $payment->status = 'expired';
                    $payment->save();

                    // Hapus referensi dari session
                    session()->forget('guest_order_reference');

                    return redirect()->route('guest.orders.confirmation', $reference)
                        ->with('error', 'Pembayaran telah kedaluwarsa. Silakan buat pesanan baru.');
                } else {
                    $payment->status = 'failed';
                    $payment->save();

                    return redirect()->route('guest.orders.confirmation', $reference)
                        ->with('error', 'Pembayaran gagal atau dibatalkan.');
                }
            } else {
                Log::error('Payment record not found for transaction_id: ' . $request->order_id);
            }
        } else {
            Log::warning('Missing transaction data in Midtrans finish callback');
        }

        return redirect()->route('guest.orders.confirmation', $reference)
            ->with('info', 'Status pembayaran akan diperbarui segera.');
    }

    /**
     * Handle Midtrans notification callback
     */
    public function handleMidtransNotification(Request $request)
    {
        try {
            $notificationBody = json_decode($request->getContent(), true);

            if (!$notificationBody) {
                Log::error('Invalid Midtrans notification format');
                return response()->json(['status' => 'error', 'message' => 'Invalid notification format'], 400);
            }

            Log::info('Midtrans notification received: ' . json_encode($notificationBody));

            $transactionStatus = $notificationBody['transaction_status'] ?? null;
            $orderId = $notificationBody['order_id'] ?? null;
            $paymentType = $notificationBody['payment_type'] ?? 'default';
            $transactionId = $notificationBody['transaction_id'] ?? null;

            if (!$orderId) {
                Log::error('Missing order_id in Midtrans notification');
                return response()->json(['status' => 'error', 'message' => 'Missing order_id'], 400);
            }

            $emailData = DB::transaction(function () use ($orderId, $paymentType, $transactionStatus, $notificationBody) {
                // Lock the payment so repeated Midtrans notifications are idempotent.
                $payment = Payment::where('transaction_id', $orderId)->lockForUpdate()->first();

                if (!$payment) {
                    Log::error('Payment record not found for transaction_id: ' . $orderId);
                    return null;
                }

                $order = $payment->order()->with('ticket')->first();
                if (!$order) {
                    Log::error('Order not found for payment ID: ' . $payment->id);
                    return null;
                }

                $shouldSendETicket = false;

                // Simpan detail metode pembayaran
                $payment->payment_method_detail = $paymentType;
                $payment->payment_type = $paymentType;
                $payment->payment_data = $notificationBody;

                // Simpan instruksi pembayaran jika ada
                if (isset($notificationBody['payment_instructions'])) {
                    $payment->payment_instruction = json_encode($notificationBody['payment_instructions']);
                }

                // Update waktu kedaluwarsa berdasarkan metode pembayaran
                if ($transactionStatus == 'pending') {
                    // Perbarui waktu kedaluwarsa berdasarkan metode pembayaran
                    $expiryDuration = $this->getExpiryDurationForPaymentType($paymentType);
                    $newExpiryTime = now()->addMinutes($expiryDuration);

                    $payment->expires_at = $newExpiryTime;
                    $order->expires_at = $newExpiryTime;
                    $order->save();

                    Log::info("Updated expiry time for order {$order->id} to {$newExpiryTime} based on payment type {$paymentType}");
                }

                // Update status pembayaran berdasarkan notifikasi
                if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                    $payment->status = 'completed';

                    // Process e-ticket hanya jika status sebelumnya bukan completed
                    if ($payment->getOriginal('status') !== 'completed') {
                        // Kurangi kuota tiket
                        $order->ticket->decrement('quota_avail', $order->quantity);
                        $shouldSendETicket = true;
                    }
                } elseif ($transactionStatus == 'pending') {
                    $payment->status = 'pending';
                } elseif ($transactionStatus == 'expire') {
                    $payment->status = 'expired';
                } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny') {
                    $payment->status = 'failed';
                }

                $payment->save();

                return [
                    'order' => $order,
                    'is_guest' => $order->user_id === null,
                    'should_send_eticket' => $shouldSendETicket,
                ];
            });

            if (!$emailData) {
                return response()->json(['status' => 'error', 'message' => 'Payment or order not found'], 404);
            }

            // Email delivery must not make the payment notification return HTTP 500.
            if ($emailData['should_send_eticket']) {
                try {
                    Mail::to($emailData['order']->email)->send(
                        new SendETicket($emailData['order'], $emailData['is_guest'])
                    );
                    Log::info('E-ticket sent to ' . ($emailData['is_guest'] ? 'guest' : 'user') . ': ' . $emailData['order']->email);
                } catch (\Throwable $e) {
                    Log::error('Payment completed but e-ticket email failed: ' . $e->getMessage(), [
                        'order_id' => $orderId,
                        'exception' => $e,
                    ]);
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error processing Midtrans notification: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Server error'], 500);
        }
    }

    /**
     * Get expiry duration based on payment type
     */
    private function getExpiryDurationForPaymentType($paymentType)
    {
        $mappings = [
            'credit_card' => 'credit_card',
            'bank_transfer' => 'bank_transfer',
            'echannel' => 'echannel',
            'gopay' => 'gopay',
            'shopeepay' => 'shopeepay',
            'qris' => 'qris',
            'cstore' => 'cstore',
            'akulaku' => 'akulaku',
            'kredivo' => 'kredivo',
        ];

        $configKey = $mappings[$paymentType] ?? 'default';
        return config("midtrans.expiry_durations.{$configKey}", 60); // Default 60 minutes if not configured
    }

    /**
     * Check and get order status via AJAX
     */
    public function checkOrderStatus(Order $order)
    {
        // Validasi akses
        if ($order->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Ambil status pembayaran terbaru
        $payment = $order->payment;

        // Auto-mark expired jika batas waktu pembayaran sudah lewat
        if ($payment && $payment->status === 'pending') {
            $paymentExpired = $payment->expires_at && now()->isAfter($payment->expires_at);
            $orderExpired = $order->expires_at && now()->isAfter($order->expires_at);

            if ($paymentExpired || $orderExpired) {
                $payment->status = 'expired';
                $payment->save();

                Log::info("Payment {$payment->id} marked as expired via checkOrderStatus for order {$order->id}");
            }
        }

        $status = $payment ? $payment->status : 'pending';

        // Data waktu kedaluwarsa terbaru
        $expiresAt = $payment && $payment->expires_at
            ? $payment->expires_at->timestamp * 1000 // Konversi ke milidetik
            : ($order->expires_at ? $order->expires_at->timestamp * 1000 : null);

        // Data metode pembayaran
        $paymentMethod = $payment ? $payment->payment_method_detail : null;

        // Data instruksi pembayaran
        $instructions = null;
        if ($payment && $payment->payment_instruction) {
            try {
                $instructions = json_decode($payment->payment_instruction, true);
            } catch (\Exception $e) {
                Log::error('Failed to decode payment instructions: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status' => $status,
            'payment_method' => $paymentMethod,
            'expires_at' => $expiresAt,
            'instructions' => $instructions
        ]);
    }

    /**
     * Display a specified payment for authenticated user
     */
    public function show(Payment $payment)
    {
        // Cek apakah payment terkait dengan order milik user yang login
        if ($payment->order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $payment->load(['order.ticket.event']);

        return view('payments.show', compact('payment'));
    }

    /**
     * Update payment status (for admin)
     */
    public function updateStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|string|in:pending,completed,expired,cancelled,failed'
        ]);

        $oldStatus = $payment->status;
        $newStatus = $request->status;

        // Log perubahan status yang akan dilakukan
        Log::info("Attempting to change payment status from {$oldStatus} to {$newStatus} for payment ID: {$payment->id}");

        // Hanya update jika status berubah
        if ($oldStatus !== $newStatus) {
            try {
                DB::beginTransaction();

                $payment->status = $newStatus;
                $payment->save();

                // Jika status diubah dari selain completed menjadi completed
                if ($oldStatus !== 'completed' && $newStatus === 'completed') {
                    // Load order dengan relasi yang diperlukan
                    $order = $payment->order->load(['ticket.event', 'user']);

                    // Validasi ketersediaan tiket sebelum mengurangi kuota
                    if ($order->ticket->quota_avail < $order->quantity) {
                        throw new \Exception("Kuota tiket tidak mencukupi. Tersedia: {$order->ticket->quota_avail}, Dibutuhkan: {$order->quantity}");
                    }

                    // Kurangi kuota tiket
                    $order->ticket->decrement('quota_avail', $order->quantity);
                    Log::info("Ticket quota decreased by {$order->quantity} for ticket ID: {$order->ticket->id}. New quota: {$order->ticket->quota_avail}");

                    // Kirim e-ticket
                    try {
                        $isGuest = $order->user_id === null;
                        Mail::to($order->email)->send(new SendETicket($order, $isGuest));
                        Log::info("E-ticket sent to " . ($isGuest ? 'guest' : 'user') . ": {$order->email}");
                    } catch (\Exception $e) {
                        Log::error("Gagal mengirim e-ticket: " . $e->getMessage());
                        // Tidak perlu throw exception karena pengiriman email bukan operasi kritis
                    }
                }

                DB::commit();

                return redirect()->route('admin.payments.index')
                    ->with('success', 'Status pembayaran berhasil diperbarui.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error updating payment status: " . $e->getMessage());

                return redirect()->back()
                    ->with('error', 'Gagal memperbarui status pembayaran: ' . $e->getMessage());
            }
        }

        // Jika status tidak berubah
        return redirect()->route('admin.payments.index')
            ->with('info', 'Status pembayaran tidak berubah.');
    }

    /**
     * Display order confirmation for guest user
     */
    public function guestConfirmation($reference)
    {
        $order = Order::where('reference', $reference)->firstOrFail();

        // Check if the reference matches the one in session
        if (!session('guest_order_reference') || session('guest_order_reference') !== $reference) {
            abort(403, 'Unauthorized action.');
        }

        return view('guest.orders.confirmation', compact('order'));
    }

    /**
     * Check and get order status via AJAX for guest
     */
    public function checkOrderStatusGuest($reference)
    {
        $order = Order::where('reference', $reference)->firstOrFail();

        // Verifikasi referensi di session
        if (!session('guest_order_reference') || session('guest_order_reference') !== $reference) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Ambil status pembayaran terbaru
        $payment = $order->payment;

        // Auto-mark expired jika batas waktu pembayaran sudah lewat
        if ($payment && $payment->status === 'pending') {
            $paymentExpired = $payment->expires_at && now()->isAfter($payment->expires_at);
            $orderExpired = $order->expires_at && now()->isAfter($order->expires_at);

            if ($paymentExpired || $orderExpired) {
                $payment->status = 'expired';
                $payment->save();

                Log::info("Payment {$payment->id} marked as expired via checkOrderStatusGuest for order {$order->id}");
            }
        }

        $status = $payment ? $payment->status : 'pending';

        // Data waktu kedaluwarsa terbaru
        $expiresAt = $payment && $payment->expires_at
            ? $payment->expires_at->timestamp * 1000 // Konversi ke milidetik
            : ($order->expires_at ? $order->expires_at->timestamp * 1000 : null);

        // Data metode pembayaran
        $paymentMethod = $payment ? $payment->payment_method_detail : null;

        // Data instruksi pembayaran
        $instructions = null;
        if ($payment && $payment->payment_instruction) {
            try {
                $instructions = json_decode($payment->payment_instruction, true);
            } catch (\Exception $e) {
                Log::error('Failed to decode payment instructions: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status' => $status,
            'payment_method' => $paymentMethod,
            'expires_at' => $expiresAt,
            'instructions' => $instructions
        ]);
    }
}
