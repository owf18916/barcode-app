<?php

namespace App\Livewire\Admin\ResultKanban;

use App\Models\ScanKanban;
use App\Traits\Swalable;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, Swalable;

    public function render()
    {
        return view('livewire.admin.result-kanban.index', [
            'resultKanbans' => ScanKanban::with(['area','kanban'])->paginate(10)
        ]);
    }
}
