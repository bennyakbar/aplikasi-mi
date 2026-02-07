<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\Fee;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show role-specific dashboard
     */
    public function index(): View
    {
        $user = auth()->user();

        // Route to appropriate dashboard based on role
        if ($user->hasRole('system_admin')) {
            return $this->systemAdminDashboard();
        } elseif ($user->hasRole('bendahara')) {
            return $this->bendaharaDashboard();
        } elseif ($user->hasRole('yayasan')) {
            return $this->yayasanDashboard();
        } elseif ($user->hasRole('petugas_transaksi')) {
            return $this->petugasTransaksiDashboard();
        } elseif ($user->hasRole('admin_master_data')) {
            return $this->adminMasterDataDashboard();
        }

        // Fallback
        return view('dashboard');
    }

    /**
     * System Admin Dashboard
     */
    protected function systemAdminDashboard(): View
    {
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_students' => Student::count(),
            'total_payments_today' => Payment::whereDate('payment_date', today())->count(),
        ];

        return view('dashboard.system-admin', compact('stats'));
    }

    /**
     * Bendahara Dashboard
     */
    public function bendaharaDashboard(): View
    {
        // Today's income
        $todayIncome = Payment::whereDate('payment_date', today())
            ->sum('total_amount');

        // This month's income
        $monthIncome = Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('total_amount');

        // Students with outstanding payments (tunggakan)
        $studentsWithTunggakan = $this->getStudentsWithTunggakan();

        // Monthly trend (last 6 months)
        $monthlyTrend = Payment::select(
            DB::raw('EXTRACT(MONTH FROM payment_date) as month'),
            DB::raw('EXTRACT(YEAR FROM payment_date) as year'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('payment_date', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Pending corrections count
        $pendingCorrections = 0; // Will be implemented later

        return view('dashboard.bendahara', compact(
            'todayIncome',
            'monthIncome',
            'studentsWithTunggakan',
            'monthlyTrend',
            'pendingCorrections'
        ));
    }

    /**
     * Yayasan Dashboard (Read-only)
     */
    public function yayasanDashboard(): View
    {
        // Total income this academic year
        $yearlyIncome = Payment::whereYear('payment_date', now()->year)
            ->sum('total_amount');

        // Payment completion percentage
        $totalStudents = Student::where('status', 'active')->count();
        $studentsWithTunggakan = $this->getStudentsWithTunggakan()->count();
        $completionRate = $totalStudents > 0 
            ? round((($totalStudents - $studentsWithTunggakan) / $totalStudents) * 100, 1) 
            : 0;

        // Monthly trend
        $monthlyTrend = Payment::select(
            DB::raw('EXTRACT(MONTH FROM payment_date) as month'),
            DB::raw('SUM(total_amount) as total')
        )
            ->whereYear('payment_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Summary by category
        $categoryStats = DB::table('payments')
            ->join('students', 'payments.student_id', '=', 'students.id')
            ->join('student_categories', 'students.category_id', '=', 'student_categories.id')
            ->select('student_categories.name as category', DB::raw('SUM(payments.total_amount) as total'))
            ->whereYear('payments.payment_date', now()->year)
            ->groupBy('student_categories.name')
            ->get();

        return view('dashboard.yayasan', compact(
            'yearlyIncome',
            'completionRate',
            'monthlyTrend',
            'categoryStats',
            'totalStudents',
            'studentsWithTunggakan'
        ));
    }

    /**
     * Petugas Transaksi Dashboard
     */
    protected function petugasTransaksiDashboard(): View
    {
        $todayPayments = Payment::with('student')
            ->whereDate('payment_date', today())
            ->where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        $todayTotal = Payment::whereDate('payment_date', today())
            ->where('user_id', auth()->id())
            ->sum('total_amount');

        return view('dashboard.petugas-transaksi', compact('todayPayments', 'todayTotal'));
    }

    /**
     * Admin Master Data Dashboard
     */
    protected function adminMasterDataDashboard(): View
    {
        $stats = [
            'total_students' => Student::count(),
            'active_students' => Student::where('status', 'active')->count(),
            'total_categories' => \App\Models\StudentCategory::count(),
            'active_fees' => Fee::where('is_active', true)->count(),
        ];

        return view('dashboard.admin-master-data', compact('stats'));
    }

    /**
     * Get students with outstanding payments
     */
    protected function getStudentsWithTunggakan()
    {
        // Get all active students with their total fees and total payments
        return Student::where('status', 'active')
            ->with(['category', 'payments'])
            ->get()
            ->filter(function ($student) {
                // Calculate total obligation
                $totalFees = Fee::where('category_id', $student->category_id)
                    ->where('is_active', true)
                    ->sum('amount');
                
                // Calculate total paid
                $totalPaid = $student->payments->sum('total_amount');
                
                // Has tunggakan if paid < obligation
                return $totalPaid < $totalFees;
            });
    }
}
