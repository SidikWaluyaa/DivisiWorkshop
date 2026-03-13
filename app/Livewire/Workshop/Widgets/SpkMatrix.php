<?php

namespace App\Livewire\Workshop\Widgets;

use Livewire\Component;
use App\Services\WorkshopMatrixService;

class SpkMatrix extends Component
{
    public array $matrixData = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->matrixData = (new WorkshopMatrixService())->getMatrixData();
    }

    public function render()
    {
        return view('livewire.workshop.widgets.spk-matrix');
    }
}
