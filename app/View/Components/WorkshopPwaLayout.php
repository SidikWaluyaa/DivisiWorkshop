<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class WorkshopPwaLayout extends Component
{
    public ?string $title;

    public function __construct(?string $title = null)
    {
        $this->title = $title;
    }

    public function render(): View
    {
        return view('layouts.workshop-pwa');
    }
}
