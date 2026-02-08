<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Bulanan - {{ $month }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4F46E5;
        }

        .header h1 {
            font-size: 18px;
            color: #4F46E5;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 12px;
            color: #666;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .summary-box {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .summary-item {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
        }

        .summary-item .label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background: #4F46E5;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }

        td {
            font-size: 10px;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #999;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        .method-summary {
            display: table;
            width: 100%;
        }

        .method-item {
            display: table-cell;
            width: 33%;
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
            background: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN REKAP BULANAN</h1>
        <p>Periode: {{ $month }}</p>
        <p>MI / SD - Sistem Administrasi Keuangan</p>
    </div>

    <div class="section">
        <div class="section-title">RINGKASAN PENERIMAAN</div>
        <div class="summary-box">
            <div class="summary-item">
                <div class="label">Total Transaksi</div>
                <div class="value">{{ $totalPayments }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Tagihan</div>
                <div class="value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Diterima</div>
                <div class="value">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">REKAP PER METODE PEMBAYARAN</div>
        <table>
            <thead>
                <tr>
                    <th>Metode Pembayaran</th>
                    <th class="text-center">Jumlah Transaksi</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byMethod as $method => $data)
                    <tr>
                        <td>{{ ucfirst($method) }}</td>
                        <td class="text-center">{{ $data['count'] }}</td>
                        <td class="text-right">Rp {{ number_format($data['amount'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">REKAP PER JENIS BIAYA</div>
        <table>
            <thead>
                <tr>
                    <th>Jenis Biaya</th>
                    <th class="text-center">Item</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byFeeType as $type => $data)
                    <tr>
                        <td>{{ ucfirst($type) }}</td>
                        <td class="text-center">{{ $data['count'] }}</td>
                        <td class="text-right">Rp {{ number_format($data['amount'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">REKAP HARIAN</div>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th class="text-center">Transaksi</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dailySummary as $date => $data)
                    <tr>
                        <td>{{ $date }}</td>
                        <td class="text-center">{{ $data['count'] }}</td>
                        <td class="text-right">Rp {{ number_format($data['amount'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($payments->count() > 0)
        <div class="section">
            <div class="section-title">DAFTAR TRANSAKSI</div>
            <table>
                <thead>
                    <tr>
                        <th>No. Kwitansi</th>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th class="text-right">Dibayar</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->receipt_number }}</td>
                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td>{{ $payment->student->name ?? '-' }}</td>
                            <td class="text-right">Rp {{ number_format($payment->paid_amount, 0, ',', '.') }}</td>
                            <td>{{ $payment->user->name ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>Laporan digenerate pada: {{ $generatedAt }}</p>
        <p>Sistem Administrasi Keuangan TU SD/MI</p>
    </div>
</body>

</html>