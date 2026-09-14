<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Helpers\ActivityLogger;

class UserManagement extends Component
{
    use WithPagination;

    // Search & Filters
    public $search = '';
    public $filterRole = 'ALL';
    public $filterStatus = 'ALL'; // ALL, ACTIVE, INACTIVE, TRASHED
    public $filterOnline = 'ALL'; // ALL, ONLINE, OFFLINE
    public $filterSpecialization = 'ALL';
    public $perPage = 10;

    // Selection for bulk actions
    public $selected = [];
    public $selectAll = false;

    // Modal State
    public $showModal = false;
    public $isEditMode = false;
    public $selectedUserId = null;
    public $modalTab = 'personal'; // personal, access

    // Form Fields
    public $name = '';
    public $email = '';
    public $phone = '';
    public $role = 'user';
    public $is_active = true;
    public $specialization = '';
    public $station = '';
    public $workshop_pool = '';
    public $availability_status = 'tersedia';
    public $is_support = false;
    public $cs_code = '';
    public $access_rights = [];
    public $password = '';
    public $password_confirmation = '';

    // Modul Matrix & Divisions (100% Synced with Sidebar)
    public $allDivisions = [
        [
            'id' => 'general',
            'title' => 'Dashboard & Tracking',
            'badge' => 'PORTAL UMUM',
            'color' => 'blue',
            'modules' => [
                'dashboard' => 'Dashboard Utama',
                'internal-tracking' => 'Lacak SPK Internal',
                'internal-tracking.services' => 'Lacak Jasa Workshop',
            ]
        ],
        [
            'id' => 'cs',
            'title' => 'Divisi Customer Service',
            'badge' => 'DIVISI CS',
            'color' => 'teal',
            'modules' => [
                'cs.dashboard' => 'CS Dashboard',
                'cs.analytics' => 'Laporan Performa CS',
                'cs.leads.konsultasi' => 'Konsultasi',
                'cs.leads.follow-up' => 'Follow-up',
                'cs.leads.closing' => 'Closing',
                'cs.leads.followup-closing' => 'Follow Up Closing',
                'cs.pending-monitoring' => 'Kiriman SPK Pending',
                'cs.spk' => 'Data SPK',
                'cs.greeting' => 'Greeting Chat',
                'cs.after-photos' => 'Galeri After Photo',
                'cs.forecasting' => 'Forecasting',
            ]
        ],
        [
            'id' => 'gudang',
            'title' => 'Divisi Gudang & Logistik',
            'badge' => 'DIVISI GUDANG',
            'color' => 'orange',
            'modules' => [
                'gudang.outbound-receipt' => 'Penerimaan Outbound (QC ke Gudang)',
                'storage.purchase' => 'Belanja Gudang (WMS)',
                'storage.disbursement' => 'Barang Keluar',
                'storage.history' => 'Riwayat Mutasi',
                'storage.dashboard' => 'Dashboard Gudang',
                'warehouse.storage' => 'Penyimpanan Rak',
                'storage.pickup-history' => 'Riwayat Pengambilan',
                'reception' => 'Penerimaan (Reception)',
                'assessment' => 'Assessment',
                'manifest.index' => 'Logistik Manifest',
                'storage.manual' => 'Gudang Manual',
                'storage.manual.racks' => 'Rak Manual',
                'finish' => 'Finish (Gudang Finish & Pickup)',
                'shipping' => 'Pengiriman & Ekspedisi',
                'admin.custom-label' => 'Label Custom',
            ]
        ],
        [
            'id' => 'workshop',
            'title' => 'Divisi Workshop',
            'badge' => 'PWA WORKSHOP',
            'color' => 'emerald',
            'modules' => [
                'workshop' => 'Akses PWA Divisi Workshop (Produksi Terpadu)',
            ]
        ],
        [
            'id' => 'finance',
            'title' => 'Divisi Finance',
            'badge' => 'DIVISI FINANCE',
            'color' => 'yellow',
            'modules' => [
                'finance.dashboard' => 'Dashboard Finance',
                'finance.waiting-payment' => 'Waiting Payment',
                'finance.transaction' => 'Finance Transaksi',
                'finance.invoices' => 'Data Invoice',
                'finance.cancelled' => 'Transaksi Batal',
                'finance.cs-verification' => 'Audit Bayar CS',
                'finance.payments' => 'Input Pembayaran',
                'finance.mutations' => 'Import Mutasi',
                'finance.verifications' => 'Verifikasi Mutasi',
            ]
        ],
        [
            'id' => 'cx',
            'title' => 'Divisi Customer Experience (CC)',
            'badge' => 'DIVISI CC',
            'color' => 'pink',
            'modules' => [
                'cx.dashboard' => 'CC Dashboard',
                'cx.index' => 'Follow Up (Worklist)',
                'cx.history' => 'History Resolusi',
                'cx.oto' => 'Kolam OTO (Upsell)',
                'cx.after-confirmation' => 'Konfirmasi After',
                'cx.shipping-monitoring' => 'Monitoring Kirim',
                'cx.overdue' => 'Overdue SLA',
                'cx.warranty-claims' => 'Inbox Klaim Garansi',
                'cx.verified-addresses' => 'Alamat Terverifikasi',
                'admin.complaints' => 'Komplain Pelanggan',
            ]
        ],
        [
            'id' => 'master',
            'title' => 'Master Data & Sistem',
            'badge' => 'MASTER DATA',
            'color' => 'purple',
            'modules' => [
                'admin.customers' => 'Master Customer',
                'admin.promotions' => 'Manajemen Promo',
                'admin.announcements' => 'Rilis & Pengumuman',
                'admin.users' => 'Pengguna & Akses',
                'admin.activity-logs' => 'Log Aktivitas',
                'admin.reports' => 'Laporan Sistem',
                'admin.performance' => 'KPI (Statistik Performa)',
                'admin.supply-chain' => 'Supply Chain Portal',
                'material-requests' => 'Pengajuan Material',
                'admin.purchases' => 'Pembelian PO',
                'admin.data-integrity' => 'Kesehatan Data (Integrity Hub)',
            ]
        ],
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'filterRole' => ['except' => 'ALL'],
        'filterStatus' => ['except' => 'ALL'],
        'filterOnline' => ['except' => 'ALL'],
        'filterSpecialization' => ['except' => 'ALL'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterRole()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterOnline()
    {
        $this->resetPage();
    }

    public function updatingFilterSpecialization()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getCurrentPageUserIds();
        } else {
            $this->selected = [];
        }
    }

    private function getCurrentPageUserIds()
    {
        return $this->getUsersQuery()->pluck('id')->map(fn($id) => (string)$id)->toArray();
    }

    public function updatedRole($value)
    {
        $this->applyPresetForRole($value);
    }

    /**
     * Select all modules within a specific division card (Sistem Paket Divisi)
     */
    public function selectDivisionPackage($divisionId)
    {
        $division = collect($this->allDivisions)->firstWhere('id', $divisionId);
        if ($division && isset($division['modules'])) {
            $keys = array_keys($division['modules']);
            $current = is_array($this->access_rights) ? $this->access_rights : [];
            $this->access_rights = array_values(array_unique(array_merge($current, $keys)));
        }
    }

    /**
     * Unselect all modules within a specific division card
     */
    public function unselectDivisionPackage($divisionId)
    {
        $division = collect($this->allDivisions)->firstWhere('id', $divisionId);
        if ($division && isset($division['modules'])) {
            $keys = array_keys($division['modules']);
            $current = is_array($this->access_rights) ? $this->access_rights : [];
            $this->access_rights = array_values(array_diff($current, $keys));
        }
    }

    public function applyPresetForRole($roleType)
    {
        $allKeys = collect($this->allDivisions)->pluck('modules')->flatMap(fn($m) => array_keys($m))->values()->toArray();
        $presets = [
            'user' => ['dashboard', 'internal-tracking'],
            'technician' => [], // Unchanged, technician managed via station/specialization
            'pic' => [],        // Unchanged
            'gudang' => [
                'dashboard', 'internal-tracking',
                'gudang.outbound-receipt', 'storage.purchase', 'storage.disbursement', 'storage.history',
                'storage.dashboard', 'warehouse.storage', 'storage.pickup-history', 'reception',
                'assessment', 'manifest.index', 'storage.manual', 'storage.manual.racks', 'finish',
                'shipping', 'admin.custom-label'
            ],
            'cs' => [
                'dashboard', 'internal-tracking',
                'cs.dashboard', 'cs.analytics', 'cs.leads.konsultasi', 'cs.leads.follow-up',
                'cs.leads.closing', 'cs.leads.followup-closing', 'cs.pending-monitoring', 'cs.spk',
                'cs.greeting', 'cs.after-photos', 'cs.forecasting'
            ],
            'workshop' => [
                'dashboard', 'internal-tracking', 'internal-tracking.services', 'workshop'
            ],
            'finance' => [
                'dashboard', 'internal-tracking',
                'finance.dashboard', 'finance.waiting-payment', 'finance.transaction', 'finance.invoices',
                'finance.cancelled', 'finance.cs-verification', 'finance.payments', 'finance.mutations',
                'finance.verifications', 'manifest.index'
            ],
            'cx' => [
                'dashboard', 'internal-tracking',
                'cx.dashboard', 'cx.index', 'cx.history', 'cx.oto', 'cx.after-confirmation',
                'cx.shipping-monitoring', 'cx.overdue', 'cx.warranty-claims', 'cx.verified-addresses',
                'admin.complaints'
            ],
            'spv' => [
                'dashboard', 'internal-tracking', 'internal-tracking.services', 'admin.performance', 'admin.reports',
                'cs.dashboard', 'storage.dashboard', 'workshop', 'finance.dashboard', 'cx.dashboard'
            ],
            'hr' => [
                'dashboard', 'admin.users', 'admin.reports', 'admin.performance'
            ],
            'admin' => $allKeys,
            'owner' => $allKeys,
        ];

        $this->access_rights = $presets[$roleType] ?? [];
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterRole', 'filterStatus', 'filterOnline', 'filterSpecialization']);
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->isEditMode = false;
        $this->selectedUserId = null;
        $this->showModal = true;
        $this->modalTab = 'personal';
    }

    public function openEditModal($userId)
    {
        $this->resetValidation();
        $user = User::withTrashed()->findOrFail($userId);
        
        $this->selectedUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->role = $user->role;
        $this->is_active = (bool)$user->is_active;
        $this->specialization = $user->specialization ?? '';
        $this->station = $user->station ?? '';
        $this->workshop_pool = $user->workshop_pool ?? '';
        $this->availability_status = $user->availability_status ?? 'tersedia';
        $this->is_support = (bool)$user->is_support;
        $this->cs_code = $user->cs_code ?? '';
        $this->access_rights = is_array($user->access_rights) ? $user->access_rights : (json_decode($user->access_rights, true) ?? []);
        $this->password = '';
        $this->password_confirmation = '';
        
        $this->isEditMode = true;
        $this->showModal = true;
        $this->modalTab = 'personal';
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->role = 'user';
        $this->is_active = true;
        $this->specialization = '';
        $this->station = '';
        $this->workshop_pool = '';
        $this->availability_status = 'tersedia';
        $this->is_support = false;
        $this->cs_code = '';
        $this->access_rights = [];
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function saveUser()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->selectedUserId),
            ],
            'phone' => 'nullable|string|max:30',
            'role' => 'required|string|in:admin,owner,hr,cs,finance,gudang,technician,pic,user,spv,cx',
            'specialization' => 'nullable|string|max:255',
            'station' => 'nullable|string|max:50',
            'workshop_pool' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_support' => 'boolean',
            'access_rights' => 'nullable|array',
            'cs_code' => 'nullable|string|max:10',
        ];

        if (!$this->isEditMode) {
            $rules['password'] = 'required|string|min:6|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:6|confirmed';
        }

        $this->validate($rules);

        $authUser = Auth::user();

        // Security role checks
        if ($this->role === 'admin' && (!$authUser || !in_array($authUser->role, ['admin', 'owner']))) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Akses Ditolak',
                'text' => 'Anda tidak memiliki wewenang untuk mengatur akun Administrator.'
            ]);
            return;
        }

        if ($this->role === 'owner' && (!$authUser || $authUser->email !== 'admin@workshop.com')) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Akses Ditolak',
                'text' => 'Hanya administrator utama (admin@workshop.com) yang dapat membuat akun Owner.'
            ]);
            return;
        }

        $specialization = in_array($this->role, ['technician', 'pic']) ? $this->specialization : null;
        $station = $this->determineStationFromSpecialization($specialization);

        if (!$this->isEditMode) {
            // Create User
            $newUser = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => $this->role,
                'is_active' => $this->is_active,
                'specialization' => $specialization,
                'station' => $station,
                'workshop_pool' => $this->workshop_pool,
                'availability_status' => $this->availability_status ?? 'tersedia',
                'is_support' => $this->is_support,
                'access_rights' => $this->access_rights ?? [],
                'cs_code' => $this->cs_code ?: null,
                'password' => Hash::make($this->password),
            ]);

            ActivityLogger::log('Membuat user baru', 'User baru dibuat: ' . $newUser->name . ' (' . $newUser->email . ') dengan role: ' . $newUser->role);

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Berhasil Ditambahkan',
                'text' => "Akun pengguna {$newUser->name} berhasil dibuat!"
            ]);
        } else {
            // Update User
            $user = User::withTrashed()->findOrFail($this->selectedUserId);

            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => ($user->id === $authUser->id) ? $user->role : $this->role,
                'is_active' => $this->is_active,
                'specialization' => $specialization,
                'station' => $station,
                'workshop_pool' => $this->workshop_pool,
                'availability_status' => $this->availability_status ?? 'tersedia',
                'is_support' => $this->is_support,
                'access_rights' => $this->access_rights ?? [],
                'cs_code' => $this->cs_code ?: null,
            ];

            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }

            $user->update($data);

            if (!$this->is_active) {
                DB::table('sessions')->where('user_id', $user->id)->delete();
            }

            ActivityLogger::log('Mengubah data user', "User {$user->name} diperbarui oleh " . ($authUser->name ?? 'Admin'));

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Berhasil Diperbarui',
                'text' => "Data pengguna {$user->name} berhasil disimpan!"
            ]);
        }

        $this->closeModal();
    }

    /**
     * Instant Toggle Active / Inactive Status
     */
    public function toggleStatus($userId)
    {
        $authUser = Auth::user();
        $user = User::withTrashed()->findOrFail($userId);

        if ($user->id === $authUser->id) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Aksi Ditolak',
                'text' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.'
            ]);
            return;
        }

        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);

        if (!$newStatus) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        $statusStr = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLogger::log('Ubah status keaktifan user', "User {$user->name} {$statusStr}.");

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Status Diperbarui',
            'text' => "User {$user->name} berhasil {$statusStr}!"
        ]);
    }

    /**
     * Soft Delete (Hide/Trash) User
     */
    public function deleteUser($userId)
    {
        $authUser = Auth::user();
        $user = User::withTrashed()->findOrFail($userId);

        if ($user->id === $authUser->id) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Aksi Ditolak',
                'text' => 'Anda tidak dapat menghapus akun Anda sendiri.'
            ]);
            return;
        }

        if ($user->role === 'admin' && (!$authUser || !in_array($authUser->role, ['admin', 'owner']))) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Akses Ditolak',
                'text' => 'Hanya Administrator/Owner yang dapat menghapus akun admin.'
            ]);
            return;
        }

        // Soft Delete and deactivate
        $userName = $user->name;
        $user->update(['is_active' => false]);
        $user->delete();

        DB::table('sessions')->where('user_id', $user->id)->delete();
        ActivityLogger::log('Menghapus user (Soft Delete)', "User {$userName} dihapus (masuk arsip/sampah).");

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Akun Dihapus',
            'text' => "Akun {$userName} berhasil dihapus dari daftar aktif."
        ]);
    }

    /**
     * Restore Soft-Deleted User
     */
    public function restoreUser($userId)
    {
        $user = User::onlyTrashed()->findOrFail($userId);
        $user->restore();
        $user->update(['is_active' => true]);

        ActivityLogger::log('Memulihkan user', "User {$user->name} berhasil dipulihkan dari arsip.");

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Akun Dipulihkan',
            'text' => "Akun {$user->name} berhasil dipulihkan dan diaktifkan kembali!"
        ]);
    }

    /**
     * Permanent Force Delete User
     */
    public function forceDeleteUser($userId)
    {
        $authUser = Auth::user();
        $user = User::withTrashed()->findOrFail($userId);

        if ($user->id === $authUser->id) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Aksi Ditolak',
                'text' => 'Anda tidak dapat menghapus permanen akun Anda sendiri.'
            ]);
            return;
        }

        if (!$authUser || !in_array($authUser->role, ['admin', 'owner'])) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Akses Ditolak',
                'text' => 'Hanya Owner / Super Administrator yang berhak menghapus akun secara permanen.'
            ]);
            return;
        }

        $userName = $user->name;
        DB::table('sessions')->where('user_id', $user->id)->delete();
        $user->forceDelete();

        ActivityLogger::log('Hapus permanen user', "User {$userName} dihapus permanen dari basis data.");

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Dihapus Permanen',
            'text' => "Akun {$userName} telah dihapus permanen dari sistem."
        ]);
    }

    /**
     * Bulk Delete Selected Users
     */
    public function bulkDelete()
    {
        $authUser = Auth::user();
        $ids = array_diff($this->selected, [$authUser->id]);

        if (empty($ids)) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Tidak Ada Data',
                'text' => 'Pilih setidaknya satu akun untuk dihapus.'
            ]);
            return;
        }

        $users = User::whereIn('id', $ids)->get();
        foreach ($users as $user) {
            $user->update(['is_active' => false]);
            $user->delete();
        }

        DB::table('sessions')->whereIn('user_id', $ids)->delete();
        ActivityLogger::log('Hapus massal user', 'Menghapus ' . count($ids) . ' akun user.');

        $this->selected = [];
        $this->selectAll = false;

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Hapus Massal Berhasil',
            'text' => count($ids) . ' akun pengguna berhasil dihapus!'
        ]);
    }

    private function determineStationFromSpecialization(?string $specialization): ?string
    {
        if (!$specialization) return null;

        $spec = strtolower(trim($specialization));

        if (in_array($spec, ['washing', 'persiapan (cuci)', 'bongkar sol', 'bongkar upper', 'prep sol', 'prep upper', 'preparation'])) {
            return 'PREPARATION';
        }
        if (in_array($spec, ['reparasi sol', 'sol repair', 'pic material sol', 'soling'])) {
            return 'SOLING';
        }
        if (in_array($spec, ['reparasi upper', 'upper repair', 'pic material upper', 'repaint', 'jahit', 'upper'])) {
            return 'UPPER';
        }
        if (in_array($spec, ['reparasi treatment', 'treatment', 'clean up', 'cleaning'])) {
            return 'TREATMENT';
        }
        if (in_array($spec, ['qc jahit', 'qc cleanup', 'qc final', 'pic qc', 'qc'])) {
            return 'QC';
        }

        return null;
    }

    private function getUsersQuery()
    {
        $query = User::query();

        // Trash filter
        if ($this->filterStatus === 'TRASHED') {
            $query->onlyTrashed();
        } elseif ($this->filterStatus === 'ACTIVE') {
            $query->where('is_active', true);
        } elseif ($this->filterStatus === 'INACTIVE') {
            $query->where('is_active', false);
        }

        // Search text filter
        if (!empty($this->search)) {
            $search = trim($this->search);
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%")
                  ->orWhere('cs_code', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($this->filterRole !== 'ALL') {
            $query->where('role', $this->filterRole);
        }

        // Online filter
        if ($this->filterOnline === 'ONLINE') {
            $query->where('last_active_at', '>=', now()->subMinutes(5));
        } elseif ($this->filterOnline === 'OFFLINE') {
            $query->where(function ($q) {
                $q->where('last_active_at', '<', now()->subMinutes(5))
                  ->orWhereNull('last_active_at');
            });
        }

        // Specialization filter
        if ($this->filterSpecialization !== 'ALL') {
            $query->where('specialization', $this->filterSpecialization);
        }

        return $query->latest();
    }

    public function render()
    {
        $users = $this->getUsersQuery()->paginate($this->perPage);

        $specializations = User::whereNotNull('specialization')
            ->where('specialization', '!=', '')
            ->distinct()
            ->pluck('specialization');

        $counts = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'online' => User::where('last_active_at', '>=', now()->subMinutes(5))->count(),
            'trashed' => User::onlyTrashed()->count(),
        ];

        return view('livewire.admin.user-management', [
            'users' => $users,
            'specializations' => $specializations,
            'counts' => $counts,
        ]);
    }
}
