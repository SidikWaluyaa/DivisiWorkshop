<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class TechnicianSkillMatrix extends Component
{
    public $selectedTechnicianId = null;
    public $selectedStation = 'SOLING';
    public $assignedServiceIds = [];
    public $searchService = '';
    public $stationFilter = 'ALL'; // 'ALL', 'PREPARATION', 'SOLING', 'UPPER', 'TREATMENT', 'QC'

    public function mount()
    {
        $firstTech = User::where('role', 'technician')->first();
        if ($firstTech) {
            $this->selectTechnician($firstTech->id);
        }
    }

    public function selectTechnician($id)
    {
        $this->selectedTechnicianId = $id;
        $tech = User::find($id);
        if ($tech) {
            $this->selectedStation = $tech->station ?: 'SOLING';
            $this->assignedServiceIds = $tech->services()->pluck('services.id')->toArray();
        }
    }

    public function updateStation($station)
    {
        $this->selectedStation = $station;
        if ($this->selectedTechnicianId) {
            $tech = User::find($this->selectedTechnicianId);
            if ($tech) {
                $tech->station = $station;
                $tech->save();
                session()->flash('success', "Stasiun utama {$tech->name} berhasil diubah ke [{$station}].");
            }
        }
    }

    public function toggleService($serviceId)
    {
        if (in_array($serviceId, $this->assignedServiceIds)) {
            $this->assignedServiceIds = array_diff($this->assignedServiceIds, [$serviceId]);
        } else {
            $this->assignedServiceIds[] = $serviceId;
        }

        $this->saveSkills();
    }

    public function selectAllCategory($categoryName)
    {
        $categoryServices = Service::where('category', $categoryName)->pluck('id')->toArray();
        $this->assignedServiceIds = array_unique(array_merge($this->assignedServiceIds, $categoryServices));
        $this->saveSkills();
    }

    public function deselectAllCategory($categoryName)
    {
        $categoryServices = Service::where('category', $categoryName)->pluck('id')->toArray();
        $this->assignedServiceIds = array_diff($this->assignedServiceIds, $categoryServices);
        $this->saveSkills();
    }

    public function saveSkills()
    {
        if (!$this->selectedTechnicianId) return;

        $tech = User::find($this->selectedTechnicianId);
        if ($tech) {
            $tech->services()->sync($this->assignedServiceIds);
            session()->flash('success', "Matrix skill & jasa untuk {$tech->name} berhasil diperbarui.");
        }
    }

    public function render()
    {
        // Query Technicians
        $excludedSpecs = ['PIC Material Sol', 'PIC Material Upper', 'PIC Material'];

        $techQuery = User::where('role', 'technician')
            ->where(function ($q) use ($excludedSpecs) {
                $q->whereNotIn('specialization', $excludedSpecs)
                  ->orWhereNull('specialization');
            })
            ->where('name', 'not like', '%Dr. Shoe%');

        if ($this->stationFilter !== 'ALL') {
            $techQuery->where('station', $this->stationFilter);
        }

        $technicians = $techQuery->orderBy('name')->get();
        $selectedTech = User::find($this->selectedTechnicianId);

        // Query Master Services Grouped by Category
        $serviceQuery = Service::orderBy('category')->orderBy('name');
        if (!empty($this->searchService)) {
            $serviceQuery->where('name', 'like', '%' . $this->searchService . '%');
        }
        $servicesGrouped = $serviceQuery->get()->groupBy('category');

        return view('livewire.admin.technician-skill-matrix', [
            'technicians' => $technicians,
            'selectedTech' => $selectedTech,
            'servicesGrouped' => $servicesGrouped,
        ])->layout('layouts.workshop-pwa');
    }
}
