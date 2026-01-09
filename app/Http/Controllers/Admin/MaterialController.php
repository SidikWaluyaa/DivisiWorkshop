<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\User;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::with('pic')->latest()->paginate(10);
        $pics = User::where('role', 'pic')->get();
        return view('admin.materials.index', compact('materials', 'pics'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'category' => 'required|string|in:Material Sol,Material Upper,Umum',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'status' => 'required|string|in:Ready,Belanja,Followup,Reject,Retur',
            'pic_user_id' => 'nullable|exists:users,id',
        ]);

        Material::create($validated);

        return redirect()->route('admin.materials.index')->with('success', 'Material berhasil ditambahkan.');
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'category' => 'required|string|in:Material Sol,Material Upper,Umum',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'status' => 'required|string|in:Ready,Belanja,Followup,Reject,Retur',
            'pic_user_id' => 'nullable|exists:users,id',
        ]);

        $material->update($validated);

        return redirect()->route('admin.materials.index')->with('success', 'Material berhasil diperbarui.');
    }

    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('admin.materials.index')->with('success', 'Material berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:materials,id',
        ]);

        Material::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.materials.index')->with('success', count($request->ids) . ' material berhasil dihapus.');
    }
}
