<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', 'in:admin,owner,hr,cs,finance,gudang,technician,pic,user,spv'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'access_rights' => ['nullable', 'array'],
        ]);

        // SECURITY: Only Admin/Owner can create Admin roles
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if ($request->role === 'admin' && (!$authUser || !in_array($authUser->role, ['admin', 'owner']))) {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk membuat akun Administrator.');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'specialization' => $request->role === 'technician' ? $request->specialization : null,
            'access_rights' => $request->access_rights ?? [],
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', 'in:admin,owner,hr,cs,finance,gudang,technician,pic,user,spv'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'access_rights' => ['nullable', 'array'],
        ]);

        // SECURITY: Prevent non-admin from modifying admin accounts
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if ($user->role === 'admin' && (!$authUser || !in_array($authUser->role, ['admin', 'owner']))) {
             return redirect()->back()->with('error', 'Hanya Administrator/Owner yang dapat mengubah akun admin lain.');
        }

        // SECURITY: Only Admin/Owner can set someone to Admin role
        if ($request->role === 'admin' && $user->role !== 'admin' && (!$authUser || !in_array($authUser->role, ['admin', 'owner']))) {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk memberikan hak akses Administrator.');
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'specialization' => $request->role === 'technician' ? $request->specialization : null,
            'access_rights' => $request->access_rights ?? [],
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // SECURITY: Cannot delete self
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // SECURITY: Only Admin/Owner can delete Admin
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if ($user->role === 'admin' && (!$authUser || !in_array($authUser->role, ['admin', 'owner']))) {
             return redirect()->back()->with('error', 'Hanya Administrator/Owner yang dapat menghapus akun admin.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        User::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.users.index')->with('success', count($request->ids) . ' user berhasil dihapus.');
    }
}
