<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Fee;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TunggakanController extends Controller
{
    /**
     * Display list of students with outstanding payments
     */
    public function index(Request $request): View
    {
        $students = Student::where('status', 'active')
            ->with(['category', 'payments'])
            ->get()
            ->map(function ($student) {
                // Calculate total obligation for this student's category
                $totalFees = Fee::where('category_id', $student->category_id)
                    ->where('is_active', true)
                    ->sum('amount');

                // Calculate total paid
                $totalPaid = $student->payments->sum('total_amount');

                // Calculate tunggakan
                $tunggakan = max(0, $totalFees - $totalPaid);

                return [
                    'student' => $student,
                    'total_fees' => $totalFees,
                    'total_paid' => $totalPaid,
                    'tunggakan' => $tunggakan,
                    'percentage_paid' => $totalFees > 0 ? round(($totalPaid / $totalFees) * 100, 1) : 100,
                ];
            })
            ->filter(function ($data) {
                return $data['tunggakan'] > 0;
            })
            ->sortByDesc('tunggakan')
            ->values();

        $totalTunggakan = $students->sum('tunggakan');
        $studentsCount = $students->count();

        return view('tunggakan.index', compact('students', 'totalTunggakan', 'studentsCount'));
    }
}
