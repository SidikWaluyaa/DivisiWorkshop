<?php

namespace App\Livewire\Cx;

use App\Models\CxIssue;
use App\Models\Service;
use App\Models\MasterIssue;
use App\Models\MasterSolution;
use App\Utils\ImageHelper;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

class EditIssueModal extends Component
{
    use WithFileUploads;

    public $showEditModal = false;
    public $issueId;
    public $category;
    public $spk_number;

    // Form Fields
    public $kendala_1, $kendala_2;
    public $opsi_solusi_1, $opsi_solusi_2;
    public $desc_upper, $desc_sol, $desc_kondisi_bawaan;

    // Service Management
    public $recService1Category, $recService1Search, $recService1Price = 0;
    public $recService2Category, $recService2Search, $recService2Price = 0;
    public $sugService1Category, $sugService1Search, $sugService1Price = 0;
    public $sugService2Category, $sugService2Search, $sugService2Price = 0;

    // Photo Management
    public $existingPhotos = [];
    public $existingPhotoUrls = [];
    public $deletedPhotos = [];
    public $newPhotos = []; // Permanently added new photos
    public $photosToUpload = []; // Temporary holding area for current selection

    // Master Data
    public $masterIssues = [];
    public $masterSolutions = [];

    // Manual Entry Flags
    public $is_k1_manual = false, $is_k2_manual = false;
    public $is_os1_manual = false, $is_os2_manual = false;

    public function updatedPhotosToUpload()
    {
        $this->validate([
            'photosToUpload.*' => 'image|max:10240',
        ]);

        foreach ($this->photosToUpload as $photo) {
            $this->newPhotos[] = $photo;
        }
        
        $this->photosToUpload = []; // Clear temp area
    }

    #[On('open-edit-issue-modal')]
    #[On('window:open-edit-issue-modal')]
    public function loadIssue($id = null)
    {
        \Log::info('CX Edit Modal: loadIssue triggered', ['id' => $id]);
        if (!$id) return;

        $issue = CxIssue::with('workOrder')->find($id);
        if (!$issue) return;

        $this->issueId = $issue->id;
        $this->category = $issue->category;
        $this->spk_number = $issue->workOrder ? $issue->workOrder->spk_number : $issue->spk_number;

        $this->kendala_1 = $issue->kendala_1;
        $this->kendala_2 = $issue->kendala_2;
        $this->opsi_solusi_1 = $issue->opsi_solusi_1;
        $this->opsi_solusi_2 = $issue->opsi_solusi_2;
        $this->desc_sol = $issue->desc_sol;
        $this->desc_kondisi_bawaan = $issue->desc_kondisi_bawaan;

        // Detect Manual Entries
        $this->is_k1_manual = $this->kendala_1 && !in_array($this->kendala_1, $this->masterIssues);
        $this->is_k2_manual = $this->kendala_2 && !in_array($this->kendala_2, $this->masterIssues);
        $this->is_os1_manual = $this->opsi_solusi_1 && !in_array($this->opsi_solusi_1, $this->masterSolutions);
        $this->is_os2_manual = $this->opsi_solusi_2 && !in_array($this->opsi_solusi_2, $this->masterSolutions);

        if ($this->category === 'OVERLOAD') {
            $this->desc_upper = $issue->description;
        } else {
            $this->desc_upper = $issue->desc_upper;
        }

        // Reset Photo State
        $this->existingPhotos = $issue->photos ?: [];
        $this->existingPhotoUrls = $issue->photo_urls ?: [];
        $this->deletedPhotos = [];
        $this->newPhotos = [];
        $this->photosToUpload = [];

        $this->fetchMasterData();
        $this->parseAllServices($issue);
        
        $this->showEditModal = true;
    }

    public function fetchMasterData()
    {
        if (!$this->category || $this->category === 'OVERLOAD') {
            $this->masterIssues = [];
            $this->masterSolutions = [];
            return;
        }

        $this->masterIssues = MasterIssue::where('category', $this->category)->where('is_active', true)->orderBy('name')->get(['id', 'name'])->toArray();
        $this->masterSolutions = MasterSolution::where('category', $this->category)->where('is_active', true)->orderBy('name')->get(['id', 'name'])->toArray();
    }

    public function parseAllServices($issue)
    {
        $services = Service::all();
        $parse = function($str) use ($services) {
            if (!$str) return ['name' => '', 'price' => 0, 'cat' => ''];
            $match = preg_match('/(.+) \(Rp ([\d.]+)\)/', $str, $matches);
            $name = trim($str);
            $price = 0;
            if ($match) {
                $name = trim($matches[1]);
                $price = (int) str_replace('.', '', $matches[2]);
            }
            $service = $services->first(fn($s) => strtolower(trim($s->name)) === strtolower($name));
            return ['name' => $service ? $service->name : $name, 'price' => $price ?: ($service ? $service->price : 0), 'cat' => $service ? $service->category : ''];
        };

        $r1 = $parse($issue->rec_service_1);
        $this->recService1Category = $r1['cat']; $this->recService1Search = $r1['name']; $this->recService1Price = $r1['price'];
        $r2 = $parse($issue->rec_service_2);
        $this->recService2Category = $r2['cat']; $this->recService2Search = $r2['name']; $this->recService2Price = $r2['price'];
        $s1 = $parse($issue->sug_service_1);
        $this->sugService1Category = $s1['cat']; $this->sugService1Search = $s1['name']; $this->sugService1Price = $s1['price'];
        $s2 = $parse($issue->sug_service_2);
        $this->sugService2Category = $s2['cat']; $this->sugService2Search = $s2['name']; $this->sugService2Price = $s2['price'];
    }

    public function selectOption($field, $value)
    {
        $flagMap = [
            'kendala_1' => 'is_k1_manual',
            'kendala_2' => 'is_k2_manual',
            'opsi_solusi_1' => 'is_os1_manual',
            'opsi_solusi_2' => 'is_os2_manual',
        ];

        if ($value === 'Lainnya') {
            $this->{$field} = ''; // Kosongkan agar user bisa ketik manual
            if (isset($flagMap[$field])) $this->{$flagMap[$field]} = true;
        } else {
            $this->{$field} = $value;
            if (isset($flagMap[$field])) $this->{$flagMap[$field]} = false;
        }
    }

    public function selectService($type, $index, $name, $price)
    {
        $key = ($type === 'rec' ? 'recService' : 'sugService') . $index;
        $this->{$key . 'Search'} = $name;
        $this->{$key . 'Price'} = $price;
    }

    public function removeExistingPhoto($index)
    {
        $this->deletedPhotos[] = $this->existingPhotos[$index];
        unset($this->existingPhotos[$index]);
        unset($this->existingPhotoUrls[$index]);
        $this->existingPhotos = array_values($this->existingPhotos);
        $this->existingPhotoUrls = array_values($this->existingPhotoUrls);
    }

    public function removeNewPhoto($index)
    {
        unset($this->newPhotos[$index]);
        $this->newPhotos = array_values($this->newPhotos);
    }

    public function save()
    {
        $this->validate(['category' => 'required']);
        $issue = CxIssue::find($this->issueId);
        if (!$issue) return;

        $currentPhotos = $this->existingPhotos;
        foreach ($this->deletedPhotos as $path) {
            $relativePath = str_replace('storage/', '', $path);
            if (Storage::disk('public')->exists($relativePath)) Storage::disk('public')->delete($relativePath);
        }

        foreach ($this->newPhotos as $index => $photo) {
            $filename = 'CX_ISSUE_EDIT_' . $this->spk_number . '_' . time() . '_' . $index;
            $currentPhotos[] = ImageHelper::convertToJpg($photo, 'cx-issues', $filename);
        }

        $formatService = fn($name, $price) => $name ? "{$name} (Rp " . number_format($price, 0, ',', '.') . ")" : '';
        $rec1 = $formatService($this->recService1Search, $this->recService1Price);
        $rec2 = $formatService($this->recService2Search, $this->recService2Price);
        $sug1 = $formatService($this->sugService1Search, $this->sugService1Price);
        $sug2 = $formatService($this->sugService2Search, $this->sugService2Price);

        $recServices = ($rec1 || $rec2) ? "1. {$rec1}\n2. {$rec2}" : "-";
        $sugServices = ($sug1 || $sug2) ? "1. {$sug1}\n2. {$sug2}" : "-";

        if ($this->category === 'OVERLOAD') {
            $description = $this->desc_upper;
            $kText = "-"; $oText = "-";
        } else {
            $kText = ($this->kendala_1 || $this->kendala_2) ? ($this->kendala_1 . "\n" . $this->kendala_2) : "-\n";
            $oText = ($this->opsi_solusi_1 || $this->opsi_solusi_2) ? ($this->opsi_solusi_1 . "\n" . $this->opsi_solusi_2) : "-\n";
            $description = "Kendala:\n" . $kText . "\nOpsi Solusi:\n" . $oText;
        }

        $issue->update([
            'category' => $this->category,
            'kendala_1' => $this->kendala_1,
            'kendala_2' => $this->kendala_2,
            'opsi_solusi_1' => $this->opsi_solusi_1,
            'opsi_solusi_2' => $this->opsi_solusi_2,
            'desc_upper' => $this->desc_upper,
            'desc_sol' => $this->desc_sol,
            'desc_kondisi_bawaan' => $this->desc_kondisi_bawaan,
            'rec_service_1' => $rec1, // Update kolom database ini
            'rec_service_2' => $rec2, // Update kolom database ini
            'sug_service_1' => $sug1, // Update kolom database ini
            'sug_service_2' => $sug2, // Update kolom database ini
            'recommended_services' => $recServices,
            'suggested_services' => $sugServices,
            'photos' => $currentPhotos,
            'kendala' => $kText,
            'opsi_solusi' => $oText,
            'description' => $description,
        ]);

        if ($issue->workOrder) $issue->workOrder->touch();
        session()->flash('success', 'Berhasil mengupdate data issue!');
        $this->dispatch('issue-updated');
        $this->showEditModal = false;
        return redirect(request()->header('Referer'));
    }

    public function getFilteredServices($category, $search)
    {
        if (!$category) return [];
        return Service::where('category', $category)->where('name', 'like', '%' . ($search ?: '') . '%')->get();
    }

    public function render()
    {
        $services = Service::all();
        $categories = $services->pluck('category')->unique()->sort();
        return view('livewire.cx.edit-issue-modal', ['services' => $services, 'categories' => $categories]);
    }
}
