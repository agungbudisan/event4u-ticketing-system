<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analitik Acara - {{ $event->title }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
        }
        h1 {
            text-align: center;
            color: #000000;
        }
        .header {
            text-align: center;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th, .table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .table th {
            background-color: #f7fafc;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            margin-top: 50px;
            color: #6B7280;
        }
    </style>
</head>
<body>
    <h1 class="text-lg font-semibold text-gray-900">Analitik Acara - {{ $event->title }}</h1>
    <div class="header mt-4">
        <p class="text-lg">Total Penjualan: Rp{{ number_format($totalSales, 0, ',', '.') }}</p>
        <p class="text-lg">Total Tiket Terjual: {{ $totalTicketsSold }}</p>
    </div>

    <table class="table mt-2">
        <thead>
            <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                <th class="bg-gray-100 px-4 py-2">Kategori Tiket</th>
                <th class="bg-gray-100 px-4 py-2">Tiket Terjual</th>
                <th class="bg-gray-100 px-4 py-2">Pendapatan</th>
                <th class="bg-gray-100 px-4 py-2">Kuota</th>
                <th class="bg-gray-100 px-4 py-2">Persentase Terjual</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ticketTypes as $ticket)
            <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                <td class="px-4 py-2">{{ $ticket['name'] }}</td>
                <td class="px-4 py-2 text-center">{{ $ticket['sold'] }}</td>
                <td class="px-4 py-2 text-right">Rp{{ number_format($ticket['revenue'], 0, ',', '.') }}</td>
                <td class="px-4 py-2 text-center">{{ $ticket['quota'] }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($ticket['percentage'], 2) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer mt-6">
        <p>Didownload pada {{ now()->setTimezone('Asia/Jakarta')->format('d M Y H:i') }}</p>
    </div>
</body>
</html>
