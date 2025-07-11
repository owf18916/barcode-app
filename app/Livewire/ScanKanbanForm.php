<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ScanKanban;
use App\Models\Kanban;
use App\Traits\Swalable;

class ScanKanbanForm extends Component
{
    use Swalable;

    public $kanbanCode = '';
    public $errorMessages = [];
    public $validArea = true;
    public $validKanban = true;

    public function submit()
    {
        $this->validate(['kanbanCode' => 'required']);

        $kanban = Kanban::where('code', $this->kanbanCode)->first();

        if(!empty($kanban)) {
            $kanbanId = $kanban->id;

            if (!$kanban->is_active) {
                $this->validKanban = false;
                $this->errorMessages[] = 'Kanban tidak aktif.';
            }

            if ($kanban->area_id !== session('area_id')) {
                $this->validArea = false;
                $this->errorMessages[] = 'Area tidak sama dengan database kanban, database kanban : '.$kanban->area->name;
            }
        } else {
            $this->validArea = false;
            $this->validKanban = false;
            $this->errorMessages[] = 'Kanban tidak valid';
            $kanbanId = null;
        }

        ScanKanban::create([
            'nik' => session('nik'),
            'area_id' => session('area_id'),
            'kanban_id' => $kanbanId,
            'scanned_kanban' => $this->kanbanCode,
            'valid_kanban' => $this->validKanban,
            'valid_area' => $this->validArea
        ]);

        if (!empty($this->errorMessages)) {
            $errorStack = '<ul>';

            foreach ($this->errorMessages as $message) {
                $errorStack .= '<li> • '.$message.'</li>';
            }

            $errorStack.'</ul>';

            $this->flashError('Scan Gagal Periksa Kembali Error berikut : ', $errorStack);
        } else {
            $this->toastSuccess('Scan berhasil.');
        }

        $this->reset('kanbanCode', 'errorMessages', 'validKanban', 'validArea');
    }

    public function render()
    {
        return view('livewire.scan-kanban-form');
    }
}

