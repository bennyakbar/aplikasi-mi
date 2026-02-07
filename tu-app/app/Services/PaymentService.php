<?php

namespace App\Services;

use App\Models\Fee;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Create a new payment with items
     */
    public function createPayment(array $data, array $items): Payment
    {
        return DB::transaction(function () use ($data, $items) {
            // Generate receipt number
            $data['receipt_number'] = Payment::generateReceiptNumber();

            // Calculate total from items
            $totalAmount = collect($items)->sum('amount');
            $data['total_amount'] = $totalAmount;
            $data['paid_amount'] = $data['paid_amount'] ?? $totalAmount;

            // Create payment
            $payment = Payment::create($data);

            // Create payment items
            foreach ($items as $item) {
                $payment->items()->create($item);
            }

            // Auto-create journal entry
            $this->accountingService->createPaymentJournal($payment);

            return $payment->load('items', 'student', 'user');
        });
    }

    /**
     * Get student fees with payment status for a given period
     */
    public function getStudentFeeStatus(Student $student, string $academicYear, ?int $month = null): array
    {
        $fees = Fee::where('category_id', $student->category_id)
            ->where('academic_year', $academicYear)
            ->where('is_active', true)
            ->get();

        $result = [];

        foreach ($fees as $fee) {
            $query = PaymentItem::whereHas('payment', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })->where('fee_id', $fee->id);

            if ($month && $fee->fee_type === 'monthly') {
                $query->where('period_month', $month);
            }

            $paidAmount = $query->sum('amount');

            $requiredAmount = $fee->fee_type === 'monthly'
                ? $fee->amount  // Per month
                : $fee->amount; // Full for yearly/one_time

            $result[] = [
                'fee' => $fee,
                'required' => $requiredAmount,
                'paid' => $paidAmount,
                'remaining' => max(0, $requiredAmount - $paidAmount),
                'status' => $paidAmount >= $requiredAmount ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid'),
            ];
        }

        return $result;
    }

    /**
     * Get payment history for a student
     */
    public function getStudentPaymentHistory(Student $student)
    {
        return Payment::with(['items.fee', 'user'])
            ->where('student_id', $student->id)
            ->orderBy('payment_date', 'desc')
            ->get();
    }
}
