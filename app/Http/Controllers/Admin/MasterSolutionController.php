<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterSolutionController extends Controller
{
    public function index()
    {
        $solutions = \App\Models\MasterSolution::orderBy('category')->orderBy('name')->get();
        return view('admin.master-solutions.index', compact('solutions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|in:TEKNIS,MATERIAL,OVERLOAD,QC,KONFIRMASI',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        \App\Models\MasterSolution::create($validated);

        return redirect()->route('admin.master-solutions.index')->with('success', 'Master Solusi berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $solution = \App\Models\MasterSolution::findOrFail($id);
        
        $validated = $request->validate([
            'category' => 'required|in:TEKNIS,MATERIAL,OVERLOAD,QC,KONFIRMASI',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $solution->update($validated);

        return redirect()->route('admin.master-solutions.index')->with('success', 'Master Solusi berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $solution = \App\Models\MasterSolution::findOrFail($id);
        $solution->delete();
        
        return redirect()->route('admin.master-solutions.index')->with('success', 'Master Solusi berhasil dihapus.');
    }

    public function toggleActive(string $id)
    {
        $solution = \App\Models\MasterSolution::findOrFail($id);
        $solution->is_active = !$solution->is_active;
        $solution->save();

        return response()->json(['success' => true, 'is_active' => $solution->is_active]);
    }

    public function apiFetch(Request $request)
    {
        try {
            $query = \App\Models\MasterSolution::where('is_active', true);
            if ($request->has('category') && !empty($request->category)) {
                $query->where('category', $request->category);
            }
            $solutions = $query->orderBy('name')->get(['id', 'name', 'category']);
            return response()->json(['success' => true, 'data' => $solutions]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('API Fetch MasterSolution Error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
