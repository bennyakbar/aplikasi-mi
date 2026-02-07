<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentCorrection;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class PaymentCorrectionController extends Controller
{
    /**
     * Display list of correction requests (for Bendahara)
     */
    public function index(): View
    {
        $corrections = PaymentCorrection::with(['payment.student', 'requester'])
            ->latest()
            ->paginate(20);

        $pendingCount = PaymentCorrection::pending()->count();

        return view('corrections.index', compact('corrections', 'pendingCount'));
    }

    /**
     * Show form to create correction request (for Petugas Transaksi)
     */
    public function create(Payment $payment): View
    {
        return view('corrections.create', compact('payment'));
    }

    /**
     * Store a new correction request
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'type' => 'required|in:void,edit',
            'reason' => 'required|string|min:10|max:500',
            'new_amount' => 'nullable|required_if:type,edit|numeric|min:0',
        ]);

        $payment = Payment::findOrFail($validated['payment_id']);

        // Check if there's already a pending correction for this payment
        $existingPending = PaymentCorrection::where('payment_id', $payment->id)
            ->pending()
            ->exists();

        if ($existingPending) {
            return back()->withErrors(['payment_id' => 'Sudah ada permintaan koreksi yang pending untuk pembayaran ini.']);
        }

        $oldValues = [
            'total_amount' => $payment->total_amount,
            'paid_amount' => $payment->paid_amount,
        ];

        $newValues = null;
        if ($validated['type'] === 'edit') {
            $newValues = [
                'total_amount' => $validated['new_amount'],
                'paid_amount' => $validated['new_amount'],
            ];
        }

        PaymentCorrection::create([
            'payment_id' => $payment->id,
            'requested_by' => auth()->id(),
            'type' => $validated['type'],
            'reason' => $validated['reason'],
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'status' => 'pending',
        ]);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Permintaan koreksi berhasil dikirim.');
    }

    /**
     * Show correction detail
     */
    public function show(PaymentCorrection $correction): View
    {
        $correction->load(['payment.student', 'payment.items.fee', 'requester', 'reviewer']);

        return view('corrections.show', compact('correction'));
    }

    /**
     * Approve a correction request
     */
    public function approve(PaymentCorrection $correction): RedirectResponse
    {
        if (!$correction->isPending()) {
            return back()->withErrors(['status' => 'Koreksi ini sudah diproses.']);
        }

        DB::beginTransaction();
        try {
            $payment = $correction->payment;

            if ($correction->type === 'void') {
                // Void the payment - create reversing journal entries
                // Mark payment as voided (soft delete or status change)
                $payment->delete();
            } else {
                // Edit the payment amount
                $payment->update([
                    'total_amount' => $correction->new_values['total_amount'],
                    'paid_amount' => $correction->new_values['paid_amount'],
                ]);
            }

            $correction->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('corrections.index')
                ->with('success', 'Koreksi berhasil disetujui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject a correction request
     */
    public function reject(Request $request, PaymentCorrection $correction): RedirectResponse
    {
        if (!$correction->isPending()) {
            return back()->withErrors(['status' => 'Koreksi ini sudah diproses.']);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        $correction->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->route('corrections.index')
            ->with('success', 'Koreksi berhasil ditolak.');
    }
}
