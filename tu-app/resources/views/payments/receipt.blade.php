<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi {{ $payment->receipt_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .receipt {
            width: 80mm;
            max-width: 100%;
            margin: 0 auto;
            padding: 10mm;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 2px dashed #333;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10px;
            color: #666;
        }

        .receipt-number {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
            padding: 5px;
            background: #f5f5f5;
        }

        .info-section {
            margin: 10px 0;
            padding: 10px 0;
            border-bottom: 1px dashed #ccc;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .info-label {
            color: #666;
        }

        .items-table {
            width: 100%;
            margin: 10px 0;
            border-collapse: collapse;
        }

        .items-table th,
        .items-table td {
            padding: 5px;
            text-align: left;
            border-bottom: 1px dotted #ccc;
        }

        .items-table th {
            font-weight: bold;
            background: #f9f9f9;
        }

        .items-table .amount {
            text-align: right;
            font-family: monospace;
        }

        .total-row {
            font-weight: bold;
            font-size: 14px;
            background: #f0f0f0;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px dashed #333;
            font-size: 10px;
            color: #666;
        }

        .signature {
            margin-top: 20px;
            text-align: right;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 40mm;
            margin-left: auto;
            margin-top: 30px;
            padding-top: 5px;
            text-align: center;
        }

        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="text-align: center; padding: 10px; background: #eee;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">🖨️ Cetak
            Kwitansi</button>
        <button onclick="window.close()"
            style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px;">✖️ Tutup</button>
    </div>

    <div class="receipt">
        <div class="header">
            <h1>KWITANSI PEMBAYARAN</h1>
            <p>Sistem Tata Usaha SD</p>
        </div>

        <div class="receipt-number">
            No: {{ $payment->receipt_number }}
        </div>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Tanggal</span>
                <span>{{ $payment->payment_date->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">NIS</span>
                <span>{{ $payment->student->nis }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Nama</span>
                <span>{{ $payment->student->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kelas</span>
                <span>{{ $payment->student->grade }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kategori</span>
                <span>{{ $payment->student->category->code }}</span>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th class="amount">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payment->items as $item)
                    <tr>
                        <td>
                            {{ $item->fee->name }}
                            @if($item->period_month)
                                <br><small style="color:#666">{{ $item->formatted_period }}</small>
                            @endif
                        </td>
                        <td class="amount">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td>TOTAL</td>
                    <td class="amount">Rp {{ number_format($payment->paid_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Metode</span>
                <span>{{ $payment->payment_method === 'cash' ? 'Tunai' : 'Transfer' }}</span>
            </div>
            @if($payment->notes)
                <div class="info-row">
                    <span class="info-label">Catatan</span>
                    <span>{{ $payment->notes }}</span>
                </div>
            @endif
        </div>

        <div class="signature">
            <div class="signature-line">
                {{ $payment->user->name }}
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih atas pembayaran Anda</p>
            <p style="margin-top: 5px;">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>

</html>