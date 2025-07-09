<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ScanKanban;
use App\Models\Kanban;

class ScanKanbanForm extends Component
{
    public $kanbanCode = '';
    public $success = false;

    public function submit()
    {
        $this->validate(['kanbanCode' => 'required']);

        $this->success = Kanban::where('code', $this->kanbanCode)->where('active', true)->exists();

        ScanKanban::create([
            'nik' => session('nik'),
            'area_id' => session('area_id'),
            'kanban_code' => $this->kanbanCode,
            'success' => $this->success,
        ]);

        $this->dispatch('scan-result', success: $this->success);
        $this->reset('kanbanCode');
    }

    public function render()
    {
        return view('livewire.scan-kanban-form');
    }
}

