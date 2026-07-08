<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use App\Helpers\ActivityLogger;

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
            'is_active' => ['required', 'boolean'],
        ]);

        // SECURITY: Only Admin/Owner can create Admin roles
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if ($request->role === 'admin' && (!$authUser || !in_array($authUser->role, ['admin', 'owner']))) {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk membuat akun Administrator.');
        }

        // SECURITY: Only admin@workshop.com can create Owner roles
        if ($request->role === 'owner' && (!$authUser || $authUser->email !== 'admin@workshop.com')) {
            return redirect()->back()->with('error', 'Hanya administrator utama (admin@workshop.com) yang dapat membuat akun Owner.');
        }

        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'is_active' => $request->boolean('is_active'),
            'specialization' => $request->role === 'technician' ? $request->specialization : null,
            'access_rights' => $request->access_rights ?? [],
            'password' => Hash::make($request->password),
        ]);

        // Log user creation in audit trail
        ActivityLogger::log('Membuat user baru', 'User baru dibuat: ' . $newUser->name . ' (' . $newUser->email . ') dengan role: ' . $newUser->role);

        \Illuminate\Support\Facades\Log::info(sprintf(
            "Audit Trail: User %s (ID: %d, Role: %s) membuat user baru %s (ID: %d) dengan role '%s'",
            $authUser->name ?? 'Unknown',
            $authUser->id ?? 0,
            $authUser->role ?? 'None',
            $newUser->name,
            $newUser->id,
            $newUser->role
        ));

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
            'is_active' => ['required', 'boolean'],
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

        // SECURITY: Only admin@workshop.com can set someone to Owner role
        if ($request->role === 'owner' && $user->role !== 'owner' && (!$authUser || $authUser->email !== 'admin@workshop.com')) {
            return redirect()->back()->with('error', 'Hanya administrator utama (admin@workshop.com) yang dapat memberikan hak akses Owner.');
        }

        $targetRole = $request->role;
        // SECURITY: A user cannot change their own role to prevent self-privilege escalation and role loss on password change
        if ($user->id === $authUser->id) {
            $targetRole = $user->role;
        }

        $details = [];
        if ($user->name !== $request->name) {
            $details[] = "Nama diubah dari '{$user->name}' menjadi '{$request->name}'";
        }
        if ($user->email !== $request->email) {
            $details[] = "Email diubah dari '{$user->email}' menjadi '{$request->email}'";
        }
        if ($user->role !== $targetRole) {
            $details[] = "Role diubah dari '{$user->role}' menjadi '{$targetRole}'";
        }
        if ($user->is_active !== $request->boolean('is_active')) {
            $statusStr = $request->boolean('is_active') ? 'Aktif' : 'Nonaktif';
            $details[] = "Status keaktifan diubah menjadi {$statusStr}";
        }
        $desc = count($details) > 0 ? implode(', ', $details) : 'Mengubah profil dasar/hak akses.';
        ActivityLogger::log('Mengubah data user: ' . $user->name, $desc);

        // Log role changes in audit trail
        if ($user->role !== $targetRole) {
            \Illuminate\Support\Facades\Log::info(sprintf(
                "=== ROLE CHANGE AUDIT ===\n" .
                "Kapan: %s\n" .
                "Oleh: %s (ID: %d, Email: %s, Role: %s)\n" .
                "Target: %s (ID: %d, Email: %s)\n" .
                "Perubahan: %s => %s\n" .
                "=========================",
                now()->toIso8601String(),
                $authUser->name ?? 'Unknown',
                $authUser->id ?? 0,
                $authUser->email ?? 'Unknown',
                $authUser->role ?? 'None',
                $user->name,
                $user->id,
                $user->email,
                strtoupper($user->role),
                strtoupper($targetRole)
            ));
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $targetRole,
            'is_active' => $request->boolean('is_active'),
            'specialization' => $request->role === 'technician' ? $request->specialization : null,
            'access_rights' => $request->access_rights ?? [],
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $data['password'] = Hash::make($request->password);
            ActivityLogger::log('Reset password user: ' . $user->name, 'Password disetel ulang oleh Admin.');
        }

        $deactivating = $user->is_active && !$request->boolean('is_active');

        $user->update($data);

        if ($deactivating) {
            // Delete all sessions for this user to force immediate logout
            \Illuminate\Support\Facades\DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();
        }

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

        ActivityLogger::log('Menghapus user', 'User dihapus: ' . $user->name . ' (' . $user->email . ') dengan role: ' . $user->role);

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $userNames = User::whereIn('id', $request->ids)->pluck('name')->implode(', ');
        ActivityLogger::log('Menghapus massal user', 'Menghapus ' . count($request->ids) . ' user: ' . $userNames);

        User::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.users.index')->with('success', count($request->ids) . ' user berhasil dihapus.');
    }
}
