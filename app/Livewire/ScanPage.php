<?php

namespace App\Livewire;

use Livewire\Component;

class ScanPage extends Component
{
    public string $selectedModule = '';
    public string $loadingModule = '';

    public function selectModule($module)
    {
        if (!session('nik') || !session('area_id')) {

            $this->dispatch('missing-session');
            
            return;
        }

        $this->loadingModule = $module;

        $this->selectedModule = $module;
        $this->loadingModule = '';
    }

    public function render()
    {
        return view('livewire.scan-page');
    }
}

