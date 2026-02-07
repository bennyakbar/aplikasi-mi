<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $students = Student::with('category')->latest()->paginate(10);
        return view('students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = StudentCategory::where('is_active', true)->get();
        return view('students.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:students,nis',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:student_categories,id',
            'grade' => 'required|integer|min:1|max:6',
            'academic_year' => 'required|string|max:9',
            'status' => 'required|in:active,graduated,transferred',
        ]);

        Student::create($validated);

        return redirect()->route('students.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student): View
    {
        $student->load('category');
        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student): View
    {
        $categories = StudentCategory::where('is_active', true)->get();
        return view('students.edit', compact('student', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:student_categories,id',
            'grade' => 'required|integer|min:1|max:6',
            'academic_year' => 'required|string|max:9',
            'status' => 'required|in:active,graduated,transferred',
        ]);

        $student->update($validated);

        return redirect()->route('students.index')
            ->with('success', 'Siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }
}
