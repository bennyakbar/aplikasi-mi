<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi {{ $payment->receipt_number }}</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
            background: #f5f5f5;
        }

        .receipt {
            width: 210mm;
            height: 148mm;
            margin: 0 auto;
            padding: 8mm 12mm;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        /* Two-column layout */
        .receipt-content {
            display: flex;
            gap: 15mm;
            height: calc(100% - 25mm);
        }

        .left-column {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .right-column {
            flex: 1.2;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            display: flex;
            align-items: center;
            gap: 5mm;
            padding-bottom: 5mm;
            margin-bottom: 5mm;
            border-bottom: 2px solid #2563eb;
        }

        .header-logo {
            flex-shrink: 0;
        }

        .header-logo img {
            width: 18mm;
            height: 18mm;
            object-fit: contain;
        }

        .header-info {
            flex: 1;
            text-align: center;
        }

        .header-info h1 {
            font-size: 16pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 1mm;
            letter-spacing: 1px;
        }

        .header-info .school-name {
            font-size: 11pt;
            font-weight: 600;
            color: #333;
            margin-bottom: 1mm;
        }

        .header-info .school-address {
            font-size: 8pt;
            color: #666;
            margin-bottom: 2mm;
        }

        .receipt-number {
            display: inline-block;
            font-size: 11pt;
            font-weight: bold;
            padding: 1.5mm 4mm;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border-radius: 4px;
        }

        /* Info section */
        .info-section {
            flex: 1;
            padding: 4mm 0;
        }

        .info-section h3 {
            font-size: 10pt;
            color: #1e40af;
            margin-bottom: 3mm;
            padding-bottom: 2mm;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-row {
            display: flex;
            margin: 2mm 0;
            font-size: 10pt;
        }

        .info-label {
            width: 25mm;
            color: #666;
            flex-shrink: 0;
        }

        .info-value {
            font-weight: 500;
            color: #333;
        }

        /* Items table */
        .items-section {
            flex: 1;
        }

        .items-section h3 {
            font-size: 10pt;
            color: #1e40af;
            margin-bottom: 3mm;
            padding-bottom: 2mm;
            border-bottom: 1px solid #e5e7eb;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .items-table th {
            background: #f3f4f6;
            padding: 2mm 3mm;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }

        .items-table td {
            padding: 2mm 3mm;
            border-bottom: 1px solid #f3f4f6;
        }

        .items-table .amount {
            text-align: right;
            font-family: 'Consolas', monospace;
        }

        .items-table .period {
            font-size: 8pt;
            color: #6b7280;
        }

        .total-row {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
        }

        .total-row td {
            font-weight: bold;
            font-size: 11pt;
            padding: 3mm;
            border: none;
        }

        /* Payment info */
        .payment-info {
            margin-top: 4mm;
            padding: 3mm;
            background: #f9fafb;
            border-radius: 4px;
            font-size: 9pt;
        }

        .payment-info .row {
            display: flex;
            justify-content: space-between;
            margin: 1mm 0;
        }

        /* Signature section */
        .signature-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
            padding-top: 5mm;
        }

        .signature-box {
            text-align: center;
            width: 35mm;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 15mm;
            padding-top: 2mm;
            font-size: 9pt;
        }

        .signature-role {
            font-size: 8pt;
            color: #666;
            margin-top: 1mm;
        }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 5mm;
            left: 12mm;
            right: 12mm;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
            padding-top: 3mm;
            border-top: 1px dashed #e5e7eb;
        }

        /* Print styles */
        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }

            .receipt {
                box-shadow: none;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }
        }

        /* Screen preview */
        @media screen {
            body {
                padding: 20px;
            }

            .no-print {
                text-align: center;
                padding: 15px;
                background: #1e40af;
                margin-bottom: 20px;
                border-radius: 8px;
                max-width: 210mm;
                margin-left: auto;
                margin-right: auto;
            }

            .no-print button {
                padding: 10px 25px;
                font-size: 13px;
                cursor: pointer;
                border: none;
                border-radius: 5px;
                margin: 0 5px;
                font-weight: 500;
                transition: all 0.2s;
            }

            .btn-print {
                background: white;
                color: #1e40af;
            }

            .btn-print:hover {
                background: #f0f0f0;
            }

            .btn-close {
                background: transparent;
                color: white;
                border: 1px solid white !important;
            }

            .btn-close:hover {
                background: rgba(255, 255, 255, 0.1);
            }
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Kwitansi</button>
        <button class="btn-close" onclick="window.close()">✖️ Tutup</button>
    </div>

    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <div class="header-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Sekolah" onerror="this.style.display='none'">
            </div>
            <div class="header-info">
                <p class="school-name">MI NURUL FALAH</p>
                <p class="school-address">Jl. Contoh Alamat No. 123, Kota, Provinsi</p>
                <h1>KWITANSI PEMBAYARAN</h1>
                <div class="receipt-number">No: {{ $payment->receipt_number }}</div>
            </div>
            <div class="header-logo">
                <!-- Placeholder for balance, or second logo -->
            </div>
        </div>

        <div class="receipt-content">
            <!-- Left Column: Student Info -->
            <div class="left-column">
                <div class="info-section">
                    <h3>📋 Data Siswa</h3>
                    <div class="info-row">
                        <span class="info-label">Tanggal</span>
                        <span class="info-value">: {{ $payment->payment_date->format('d F Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">NIS</span>
                        <span class="info-value">: {{ $payment->student->nis }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nama</span>
                        <span class="info-value">: {{ $payment->student->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kelas</span>
                        <span class="info-value">: {{ $payment->student->grade }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kategori</span>
                        <span class="info-value">:
                            {{ $payment->student->category->name ?? $payment->student->category->code }}</span>
                    </div>
                </div>

                <!-- Signature -->
                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-line">Penerima</div>
                    </div>
                    <div class="signature-box">
                        <div class="signature-line">{{ $payment->user->name }}</div>
                        <div class="signature-role">Petugas</div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Payment Details -->
            <div class="right-column">
                <div class="items-section">
                    <h3>💰 Rincian Pembayaran</h3>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 60%">Keterangan</th>
                                <th class="amount">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payment->items as $item)
                                <tr>
                                    <td>
                                        {{ $item->fee->name }}
                                        @if($item->period_month)
                                            <div class="period">{{ $item->formatted_period }}</div>
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

                    <div class="payment-info">
                        <div class="row">
                            <span>Metode Pembayaran:</span>
                            <strong>{{ $payment->payment_method === 'cash' ? 'Tunai' : 'Transfer Bank' }}</strong>
                        </div>
                        @if($payment->notes)
                            <div class="row">
                                <span>Catatan:</span>
                                <span>{{ $payment->notes }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Terima kasih atas pembayaran Anda • Dicetak: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>

</html>