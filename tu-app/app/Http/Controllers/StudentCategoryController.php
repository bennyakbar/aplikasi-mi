<?php

namespace App\Http\Controllers;

use App\Models\StudentCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StudentCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = StudentCategory::latest()->paginate(10);
        return view('student-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('student-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:student_categories,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        StudentCategory::create($validated);

        return redirect()->route('student-categories.index')
            ->with('success', 'Kategori siswa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentCategory $studentCategory): View
    {
        return view('student-categories.show', compact('studentCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentCategory $studentCategory): View
    {
        return view('student-categories.edit', compact('studentCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentCategory $studentCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:student_categories,code,' . $studentCategory->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $studentCategory->update($validated);

        return redirect()->route('student-categories.index')
            ->with('success', 'Kategori siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentCategory $studentCategory): RedirectResponse
    {
        if ($studentCategory->students()->count() > 0) {
            return redirect()->route('student-categories.index')
                ->with('error', 'Tidak dapat menghapus kategori yang masih memiliki siswa.');
        }

        $studentCategory->delete();

        return redirect()->route('student-categories.index')
            ->with('success', 'Kategori siswa berhasil dihapus.');
    }
}
