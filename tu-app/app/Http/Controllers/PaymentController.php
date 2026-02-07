<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Payment;
use App\Models\Student;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of payments
     */
    public function index(): View
    {
        $payments = Payment::with(['student', 'user'])
            ->latest('payment_date')
            ->paginate(15);

        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment
     */
    public function create(Request $request): View
    {
        $students = Student::where('status', 'active')
            ->with('category')
            ->orderBy('name')
            ->get();

        $selectedStudent = null;
        $fees = collect();

        if ($request->filled('student_id')) {
            $selectedStudent = Student::with('category')->find($request->student_id);
            if ($selectedStudent) {
                $fees = Fee::where('category_id', $selectedStudent->category_id)
                    ->where('is_active', true)
                    ->get();
            }
        }

        return view('payments.create', compact('students', 'selectedStudent', 'fees'));
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,transfer',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.fee_id' => 'required|exists:fees,id',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.period_month' => 'nullable|integer|min:1|max:12',
            'items.*.period_year' => 'nullable|integer|min:2020|max:2100',
        ]);

        // Filter out items with zero amount
        $items = collect($validated['items'])->filter(fn($item) => $item['amount'] > 0)->values()->toArray();

        if (empty($items)) {
            return back()->withErrors(['items' => 'Minimal satu item pembayaran harus diisi.'])->withInput();
        }

        $paymentData = [
            'student_id' => $validated['student_id'],
            'user_id' => auth()->id(),
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
        ];

        $payment = $this->paymentService->createPayment($paymentData, $items);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Pembayaran berhasil disimpan. No. Kwitansi: ' . $payment->receipt_number);
    }

    /**
     * Display the specified payment (receipt)
     */
    public function show(Payment $payment): View
    {
        $payment->load(['student.category', 'items.fee', 'user']);
        return view('payments.show', compact('payment'));
    }

    /**
     * Remove the specified payment
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        $payment->delete();

        return redirect()->route('payments.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }

    /**
     * Print receipt
     */
    public function printReceipt(Payment $payment): View
    {
        $payment->load(['student.category', 'items.fee', 'user']);
        return view('payments.receipt', compact('payment'));
    }
}
