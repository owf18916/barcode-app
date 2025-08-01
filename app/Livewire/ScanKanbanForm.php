<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ScanKanban;
use App\Models\Kanban;
use App\Traits\Swalable;
use Illuminate\Support\Facades\Log;

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
            // Log::info('kanban-scan', ['kanban' => $kanban, 'session_area_id' => session('area_id'), 'area_truthy' => $kanban->area_id != session('area_id')]);

            $kanbanId = $kanban->id;

            if (!$kanban->is_active) {
                $this->validKanban = false;
                $this->errorMessages[] = 'Kanban tidak aktif.';
            }

            if ($kanban->area_id != session('area_id')) {
                $this->validArea = false;
                $this->errorMessages[] = 'Area tidak sama dengan database kanban, database kanban : '.$kanban->area->name;
            }
        } else {
            $this->validArea = false;
            $this->validKanban = false;
            $this->errorMessages[] = 'Kanban tidak valid';
            $kanbanId = null;
        }

        ScanKanban::updateOrCreate(
            [
                'scanned_kanban' => $this->kanbanCode,
                'area_id' => session('area_id'),
            ],
            [
                'nik' =>session('nik'),
                'kanban_id' => $kanbanId,
                'valid_kanban' => $this->validKanban,
                'valid_area' => $this->validArea
            ]
        );



        if (!empty($this->errorMessages)) {
            $errorStack = '<ul>';

            foreach ($this->errorMessages as $message) {
                $errorStack .= '<li> • '.$message.'</li>';
            }

            $errorStack.'</ul>';
            
            $this->flashWarning('Hasil scan direkam, dengan data yang tidak sesuai. Periksa kembali data berikut : ', $errorStack);
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

