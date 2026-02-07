<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\Fee;
use App\Exports\TunggakanExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Export tunggakan report to Excel
     */
    public function exportTunggakan()
    {
        $filename = 'Laporan_Tunggakan_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new TunggakanExport, $filename);
    }

    /**
     * Export rekap bulanan to PDF
     */
    public function exportRekapBulanan(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // Get payments for the month
        $payments = Payment::with(['student', 'user', 'items.fee'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date')
            ->get();

        // Calculate summary
        $totalPayments = $payments->count();
        $totalAmount = $payments->sum('total_amount');
        $totalPaid = $payments->sum('paid_amount');

        // Group by payment method
        $byMethod = $payments->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'amount' => $group->sum('paid_amount'),
            ];
        });

        // Group by fee type
        $byFeeType = [];
        foreach ($payments as $payment) {
            foreach ($payment->items as $item) {
                $feeType = $item->fee->fee_type ?? 'Lainnya';
                if (!isset($byFeeType[$feeType])) {
                    $byFeeType[$feeType] = ['count' => 0, 'amount' => 0];
                }
                $byFeeType[$feeType]['count']++;
                $byFeeType[$feeType]['amount'] += $item->amount;
            }
        }

        // Daily summary
        $dailySummary = $payments->groupBy(function ($payment) {
            return $payment->payment_date->format('d M Y');
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'amount' => $group->sum('paid_amount'),
            ];
        });

        $data = [
            'month' => $startDate->translatedFormat('F Y'),
            'payments' => $payments,
            'totalPayments' => $totalPayments,
            'totalAmount' => $totalAmount,
            'totalPaid' => $totalPaid,
            'byMethod' => $byMethod,
            'byFeeType' => $byFeeType,
            'dailySummary' => $dailySummary,
            'generatedAt' => now()->translatedFormat('d F Y H:i'),
        ];

        $pdf = Pdf::loadView('reports.rekap-bulanan', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Rekap_Bulanan_' . $startDate->format('Y-m') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Show report index page
     */
    public function index()
    {
        return view('reports.index');
    }
}
