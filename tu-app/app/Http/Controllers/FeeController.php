<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\StudentCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $fees = Fee::with('category')->latest()->paginate(10);
        return view('fees.index', compact('fees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = StudentCategory::where('is_active', true)->get();
        return view('fees.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:student_categories,id',
            'amount' => 'required|numeric|min:0',
            'academic_year' => 'required|string|max:9',
            'fee_type' => 'required|in:monthly,yearly,one_time',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Fee::create($validated);

        return redirect()->route('fees.index')
            ->with('success', 'Tarif berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Fee $fee): View
    {
        $fee->load('category');
        return view('fees.show', compact('fee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fee $fee): View
    {
        $categories = StudentCategory::where('is_active', true)->get();
        return view('fees.edit', compact('fee', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fee $fee): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:student_categories,id',
            'amount' => 'required|numeric|min:0',
            'academic_year' => 'required|string|max:9',
            'fee_type' => 'required|in:monthly,yearly,one_time',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $fee->update($validated);

        return redirect()->route('fees.index')
            ->with('success', 'Tarif berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fee $fee): RedirectResponse
    {
        $fee->delete();

        return redirect()->route('fees.index')
            ->with('success', 'Tarif berhasil dihapus.');
    }
}
