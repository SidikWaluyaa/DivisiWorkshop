<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TechnicianManagement extends Component
{
    use WithPagination;

    // Filters & Search
    public $search = '';
    public $filterStation = 'ALL';
    public $filterPool = 'ALL';
    public $filterStatus = 'ALL';

    // Modal State
    public $showModal = false;
    public $isEditMode = false;
    public $selectedTechId = null;

    // Form Fields
    public $name = '';
    public $email = '';
    public $phone = '';
    public $role = 'technician';
    public $station = 'SOLING';
    public $specialization = 'Reparasi Sol';
    public $workshop_pool = 'Workshop Abu (Production)';
    public $is_active = true;
    public $password = '';
    public $password_confirmation = '';

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->selectedTechId),
            ],
            'phone' => 'nullable|string|max:30',
            'role' => 'required|string|in:technician,technician_assistant,qc,admin',
            'station' => 'required_if:role,technician,technician_assistant,qc|nullable|string',
            'specialization' => 'nullable|string|max:255',
            'workshop_pool' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];

        if (!$this->isEditMode) {
            $rules['password'] = 'required|string|min:6|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:6|confirmed';
        }

        return $rules;
    }

    public function updatedStation($value)
    {
        // Auto-select pool based on station
        if (in_array($value, ['PREPARATION', 'SORTIR', 'QC'])) {
            $this->workshop_pool = 'Workshop Hijau (Prep, Sortir & QC)';
        } elseif (in_array($value, ['SOLING', 'UPPER', 'TREATMENT'])) {
            $this->workshop_pool = 'Workshop Abu (Production)';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStation()
    {
        $this->resetPage();
    }

    public function updatingFilterPool()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset([
            'selectedTechId', 'name', 'email', 'phone', 'role', 
            'station', 'specialization', 'workshop_pool', 
            'is_active', 'password', 'password_confirmation'
        ]);
        $this->role = 'technician';
        $this->station = 'SOLING';
        $this->specialization = 'Reparasi Sol';
        $this->workshop_pool = 'Workshop Abu (Production)';
        $this->is_active = true;

        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $tech = User::findOrFail($id);

        $this->selectedTechId = $tech->id;
        $this->name = $tech->name;
        $this->email = $tech->email;
        $this->phone = $tech->phone ?? '';
        $this->role = $tech->role ?? 'technician';
        $this->station = $tech->station ?? 'SOLING';
        $this->specialization = $tech->specialization ?? 'Reparasi Sol';
        
        // Normalize pool display
        $existingPool = $tech->workshop_pool;
        if (empty($existingPool) || str_contains(strtolower($existingPool), 'hijau') || str_contains(strtolower($existingPool), 'utama')) {
            if (in_array($this->station, ['SOLING', 'UPPER', 'TREATMENT'])) {
                $this->workshop_pool = 'Workshop Abu (Production)';
            } else {
                $this->workshop_pool = 'Workshop Hijau (Prep, Sortir & QC)';
            }
        } elseif (str_contains(strtolower($existingPool), 'abu') || str_contains(strtolower($existingPool), 'produksi')) {
            $this->workshop_pool = 'Workshop Abu (Production)';
        } else {
            $this->workshop_pool = $existingPool;
        }

        $this->is_active = (bool) $tech->is_active;
        $this->password = '';
        $this->password_confirmation = '';

        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function saveTechnician()
    {
        $validatedData = $this->validate();

        if ($this->isEditMode) {
            $tech = User::findOrFail($this->selectedTechId);
            
            $updateData = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => $this->role,
                'station' => $this->station,
                'specialization' => $this->specialization,
                'workshop_pool' => $this->workshop_pool,
                'is_active' => $this->is_active,
            ];

            if (!empty($this->password)) {
                $updateData['password'] = Hash::make($this->password);
            }

            $tech->update($updateData);

            session()->flash('success', "Data profil & operasional teknisi {$tech->name} berhasil diperbarui.");
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => $this->role,
                'station' => $this->station,
                'specialization' => $this->specialization,
                'workshop_pool' => $this->workshop_pool,
                'is_active' => $this->is_active,
                'password' => Hash::make($this->password),
            ]);

            session()->flash('success', "User Teknisi baru {$user->name} berhasil ditambahkan!");
        }

        $this->closeModal();
    }

    public function toggleActiveStatus($id)
    {
        $tech = User::findOrFail($id);
        $tech->is_active = !$tech->is_active;
        $tech->save();

        $statusText = $tech->is_active ? 'Diaktifkan' : 'Dinonaktifkan';
        session()->flash('success', "Status akun teknisi {$tech->name} berhasil diubah ke [{$statusText}].");
    }

    public function render()
    {
        $excludedSpecs = ['PIC Material Sol', 'PIC Material Upper', 'PIC Material'];

        // Base Query for active technicians (excluding material PICs & dummy system accounts)
        $baseQuery = User::whereIn('role', ['technician', 'technician_assistant', 'qc'])
            ->where(function ($q) use ($excludedSpecs) {
                $q->whereNotIn('specialization', $excludedSpecs)
                  ->orWhereNull('specialization');
            })
            ->where('name', 'not like', '%Dr. Shoe%');

        // Copy base query for paginated result list with filters
        $query = (clone $baseQuery);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('specialization', 'like', '%' . $this->search . '%')
                  ->orWhere('workshop_pool', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStation !== 'ALL') {
            $query->where('station', $this->filterStation);
        }

        if ($this->filterPool !== 'ALL') {
            if ($this->filterPool === 'HIJAU') {
                $query->where(function ($q) {
                    $q->where('workshop_pool', 'like', '%Hijau%')
                      ->orWhereIn('station', ['PREPARATION', 'SORTIR', 'QC']);
                });
            } elseif ($this->filterPool === 'ABU') {
                $query->where(function ($q) {
                    $q->where('workshop_pool', 'like', '%Abu%')
                      ->orWhereIn('station', ['SOLING', 'UPPER', 'TREATMENT']);
                });
            }
        }

        if ($this->filterStatus !== 'ALL') {
            if ($this->filterStatus === 'ACTIVE') {
                $query->where('is_active', true);
            } elseif ($this->filterStatus === 'INACTIVE') {
                $query->where('is_active', false);
            }
        }

        $technicians = $query->orderBy('name', 'asc')->paginate(12);

        // Stats Summary synchronized 100% with base technician query
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('is_active', true)->count(),
            'inactive' => (clone $baseQuery)->where('is_active', false)->count(),
            'cuci' => (clone $baseQuery)->where('station', 'PREPARATION')->count(),
            'sortir' => (clone $baseQuery)->where('station', 'SORTIR')->count(),
            'soling' => (clone $baseQuery)->where('station', 'SOLING')->count(),
            'upper' => (clone $baseQuery)->where('station', 'UPPER')->count(),
            'treatment' => (clone $baseQuery)->where('station', 'TREATMENT')->count(),
            'qc' => (clone $baseQuery)->where('station', 'QC')->count(),
        ];

        return view('livewire.admin.technician-management', [
            'technicians' => $technicians,
            'stats' => $stats,
        ])->layout('layouts.workshop-pwa');
    }
}
