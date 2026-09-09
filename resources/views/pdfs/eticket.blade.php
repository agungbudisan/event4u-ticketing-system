<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Ticket #{{ $order->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .ticket {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 30px;
        }
        .ticket-header {
            background: linear-gradient(to right, #7B0015, #AF0020);
            color: white;
            padding: 15px;
            position: relative;
        }
        .ticket-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .ticket-id {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 14px;
        }
        .ticket-body {
            padding: 20px;
        }
        .event-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .event-details {
            margin-bottom: 20px;
        }
        .event-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 3px;
        }
        .ticket-info {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 15px;
        }
        .ticket-info-label {
            color: #666;
            font-size: 12px;
            margin-bottom: 3px;
        }
        .ticket-info-value {
            font-weight: bold;
        }
        .attendee-info {
            border-top: 1px solid #eee;
            margin-top: 20px;
            padding-top: 15px;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code img {
            width: 150px;
            height: 150px;
        }
        .qr-code-text {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 15px;
            font-size: 12px;
            color: #666;
        }
        .important-note {
            background-color: #f9f9f9;
            border-left: 4px solid #7B0015;
            padding: 15px;
            margin-top: 20px;
            font-size: 12px;
        }
        .important-note h4 {
            margin-top: 0;
            margin-bottom: 5px;
            color: #7B0015;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
            padding-top: 20px;
        }
        .logo h2 {
            margin: 0;
            color: #7B0015;
            font-size: 28px;
        }
        .icon {
            display: inline-block;
            width: 16px;
            height: 16px;
            margin-right: 6px;
            background-repeat: no-repeat;
            background-size: contain;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h2>EVENT4U</h2>
        </div>

        <div class="ticket">
            <div class="ticket-header">
                <h1>E-TICKET</h1>
                <div class="ticket-id">#{{ $order->id }}</div>
            </div>

            <div class="ticket-body">
                <div class="event-title">{{ $order->ticket->event->title }}</div>

                <div class="event-details">
                    <div class="event-meta"><strong>Tanggal:</strong> {{ $order->ticket->event->start_event->format('l, d F Y') }}</div>
                    <div class="event-meta"><strong>Waktu:</strong> {{ $order->ticket->event->start_event->format('H:i') }} WIB</div>
                    <div class="event-meta"><strong>Lokasi:</strong> {{ $order->ticket->event->location }}</div>
                </div>

                <div class="ticket-info">
                    <div>
                        <div class="ticket-info-label">JENIS TIKET</div>
                        <div class="ticket-info-value">{{ $order->ticket->ticket_class }}</div>
                    </div>
                    <div>
                        <div class="ticket-info-label">JUMLAH</div>
                        <div class="ticket-info-value">{{ $order->quantity }}</div>
                    </div>
                    <div>
                        <div class="ticket-info-label">HARGA</div>
                        <div class="ticket-info-value">Rp{{ number_format($order->total_price, 2, ',', '.') }}</div>
                    </div>
                </div>

                <div class="attendee-info">
                    <div class="ticket-info-label">DATA PEMESAN</div>
                    <div class="ticket-info-value">
                        @if($order->user_id)
                            {{ $order->user->name }}
                        @else
                            {{ $order->guest_name }}
                        @endif
                    </div>
                    <div class="event-meta">{{ $order->email }}</div>
                    @if($order->guest_phone)
                        <div class="event-meta">{{ $order->guest_phone }}</div>
                    @endif
                </div>

                <div class="qr-code">
                    @if(isset($qrCodeBase64) && $qrCodeBase64)
                        <img src="{{ $qrCodeBase64 }}" alt="QR Code Tiket" style="width:150px; height:150px;">
                    @else
                        <div style="width:150px; height:150px; border:1px solid #ddd; margin:0 auto; text-align:center; line-height:150px;">
                            QR Code
                        </div>
                    @endif
                    <div class="qr-code-text">SCAN ME</div>
                </div>

                <div class="important-note">
                    <h4>Penting:</h4>
                    <ul>
                        <li>Silakan tunjukkan e-ticket ini (cetak atau digital) saat memasuki venue</li>
                        <li>Diharapkan hadir 30 menit sebelum acara dimulai</li>
                        <li>E-ticket ini tidak dapat dipindahtangankan</li>
                        <li>Berlaku untuk {{ $order->quantity }} {{ $order->quantity > 1 ? 'orang' : 'orang' }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Untuk informasi lebih lanjut atau bantuan, silakan hubungi support@event4u.com</p>
            <p>E-ticket ini diterbitkan pada {{ now()->format('d F Y, H:i') }} WIB</p>
        </div>
    </div>
</body>
</html>
