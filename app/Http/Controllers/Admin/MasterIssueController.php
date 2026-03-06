<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterIssueController extends Controller
{
    public function index()
    {
        $issues = \App\Models\MasterIssue::orderBy('category')->orderBy('name')->get();
        return view('admin.master-issues.index', compact('issues'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|in:TEKNIS,MATERIAL,KONFIRMASI',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        \App\Models\MasterIssue::create($validated);

        return redirect()->route('admin.master-issues.index')->with('success', 'Master Kendala berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $issue = \App\Models\MasterIssue::findOrFail($id);
        
        $validated = $request->validate([
            'category' => 'required|in:TEKNIS,MATERIAL,KONFIRMASI',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $issue->update($validated);

        return redirect()->route('admin.master-issues.index')->with('success', 'Master Kendala berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $issue = \App\Models\MasterIssue::findOrFail($id);
        $issue->delete();
        
        return redirect()->route('admin.master-issues.index')->with('success', 'Master Kendala berhasil dihapus.');
    }

    public function toggleActive(string $id)
    {
        $issue = \App\Models\MasterIssue::findOrFail($id);
        $issue->is_active = !$issue->is_active;
        $issue->save();

        return response()->json(['success' => true, 'is_active' => $issue->is_active]);
    }

    public function apiFetch(Request $request)
    {
        try {
            $query = \App\Models\MasterIssue::where('is_active', true);
            
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            $issues = $query->orderBy('name')->get(['id', 'name', 'category']);
            return response()->json(['success' => true, 'data' => $issues]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('API Fetch MasterIssue Error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
