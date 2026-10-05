<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Home & Events
Route::get('/', [HomeController::class, 'index'])->name('welcome');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show')->middleware('web');
Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

// Midtrans Notification Handler (harus dapat diakses secara publik tanpa autentikasi)
Route::post('payments/midtrans/notification', [PaymentController::class, 'handleMidtransNotification'])
    ->name('payments.midtrans.notification');

// Unauthenticated Order Process
Route::prefix('guest')->group(function () {
    Route::get('/tickets/{ticket}/order', [OrderController::class, 'guestCreate'])->name('guest.orders.create');
    Route::post('/tickets/{ticket}/order', [OrderController::class, 'guestStore'])->name('guest.orders.store');
    Route::get('/orders/{reference}/payment', [PaymentController::class, 'guestCreate'])->name('guest.payments.create');
    Route::post('/orders/{reference}/payment', [PaymentController::class, 'guestStore'])->name('guest.payments.store');
    Route::get('/orders/{reference}/confirmation', [OrderController::class, 'guestConfirmation'])->name('guest.orders.confirmation');
    Route::get('/orders/{reference}/download-ticket', [OrderController::class, 'downloadETicketPdfGuest'])->name('guest.orders.download-ticket');

    // Midtrans routes
    Route::get('/orders/{reference}/payment/midtrans', [PaymentController::class, 'processMidtransPaymentGuest'])
        ->name('guest.payments.midtrans');
    Route::get('/orders/{reference}/payment/midtrans/finish', [PaymentController::class, 'finishMidtransPaymentGuest'])
        ->name('guest.payments.midtrans.finish');

    // Guest payment status check (opsional, jika ingin memberi guest kemampuan untuk memeriksa status)
    Route::get('/orders/{reference}/check-status', [PaymentController::class, 'checkOrderStatusGuest'])
        ->name('guest.payments.check-status');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        // Auto-mark payment pending yang sudah lewat batas waktu sebagai expired
        \App\Models\Payment::expirePendingForUser(Auth::id());

        return view('dashboard');
    })->name('dashboard');

    Route::prefix('dashboard')->group(function () {
        // History Orders
        Route::get('/orders-history', [OrderController::class, 'index'])->name('orders.index');

        // Profile routes
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Order Process
    Route::prefix('orders')->group(function () {
        Route::get('/tickets/{ticket}/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/tickets/{ticket}', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/{order}/payment', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/{order}/payment', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/{order}/download-ticket', [OrderController::class, 'downloadETicketPdf'])->name('orders.download-ticket');

        // Midtrans routes
        Route::get('/{order}/payment/midtrans', [PaymentController::class, 'processMidtransPayment'])
            ->name('payments.midtrans');
        Route::get('/{order}/payment/midtrans/finish', [PaymentController::class, 'finishMidtransPayment'])
            ->name('payments.midtrans.finish');
    });

    // Payment status check route (untuk AJAX)
    Route::get('payments/{order}/check-status', [PaymentController::class, 'checkOrderStatus'])
        ->name('payments.check-status');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    // Dashboard & Analytics
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics/{eventId?}', [DashboardController::class, 'analytics'])->name('analytics');

    // Event Management
    Route::resource('events', EventController::class)->except(['show']);
    Route::get('/events/{event}', [EventController::class, 'adminShow'])->name('events.show');

    // Ticket Management for specific Event
    Route::prefix('events/{event}')->group(function () {
        Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
        Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    });

    Route::get('/tickets', [TicketController::class, 'eventSelector'])->name('tickets.events');

    // Ticket Resource (edit, update, destroy operations)
    Route::resource('tickets', TicketController::class)->only(['edit', 'update', 'destroy']);

    // Category Management
    Route::resource('categories', CategoryController::class);

    // Order Management
    Route::get('/orders', [OrderController::class, 'adminIndex'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'adminShow'])->name('orders.show');

    // Payment Management
    Route::get('/payments', [PaymentController::class, 'adminIndex'])->name('payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'adminShow'])->name('payments.show');
    Route::put('/payments/{payment}/update-status', [PaymentController::class, 'updateStatus'])->name('payments.updateStatus');

    // Export Routes
    // Route::get('/payments/export', [PaymentController::class, 'export'])->name('payments.export');
    // Route::get('/payments/export-pdf', [PaymentController::class, 'exportPdf'])->name('payments.export-pdf');

    Route::get('/analytics/export-excel/{eventId?}', [DashboardController::class, 'exportExcel'])->name('analytics.exportExcel');
    Route::get('/analytics/export-pdf/{eventId?}', [DashboardController::class, 'exportPdf'])->name('analytics.exportPdf');
});

require __DIR__.'/auth.php';
