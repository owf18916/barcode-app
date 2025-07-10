<?php

namespace App\Livewire\Admin\Kanban;

use App\Models\Kanban;
use App\Models\Area;
use App\Models\KanbanCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Imports\KanbanImport;
use App\Traits\Swalable;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination, WithFileUploads, Swalable;

    public string $search = '';
    public $excelFile;
    public $kanbanIdBeingEdited = null;

    public string $code = '';
    public string $kanban_category_id = '';
    public string $area_id = '';
    public string $conveyor = '';
    public string $family = '';
    public string $issue_number = '';
    public bool $is_active = true;

    public function updatingSearch() { $this->resetPage(); }

    public function applySearch() { $this->resetPage(); }

    public function resetForm()
    {
        $this->reset([
            'kanbanIdBeingEdited', 'code', 'kanban_category_id', 'area_id',
            'conveyor', 'family', 'issue_number', 'is_active'
        ]);
    }

    public function edit($id)
    {
        $kanban = Kanban::findOrFail($id);
        $this->kanbanIdBeingEdited = $kanban->id;
        $this->code = $kanban->code;
        $this->kanban_category_id = (string) $kanban->kanban_category_id;
        $this->area_id = (string) $kanban->area_id;
        $this->conveyor = $kanban->conveyor;
        $this->family = $kanban->family;
        $this->issue_number = $kanban->issue_number;
        $this->is_active = $kanban->is_active;
    }

    public function save()
    {
        $this->validate([
            'code' => 'required|unique:kanbans,code',
            'kanban_category_id' => 'required|exists:kanban_categories,id',
            'area_id' => 'required|exists:areas,id',
        ]);

        Kanban::create([
            'code' => $this->code,
            'kanban_category_id' => $this->kanban_category_id,
            'area_id' => $this->area_id,
            'conveyor' => $this->conveyor,
            'family' => $this->family,
            'issue_number' => $this->issue_number,
            'is_active' => $this->is_active,
        ]);

        $this->flashSuccess('Kanban berhasil ditambahkan.');
        $this->resetForm();
    }

    public function update()
    {
        $this->validate([
            'code' => 'required|unique:kanbans,code,' . $this->kanbanIdBeingEdited,
            'kanban_category_id' => 'required|exists:kanban_categories,id',
            'area_id' => 'required|exists:areas,id',
        ]);

        $kanban = Kanban::findOrFail($this->kanbanIdBeingEdited);
        $kanban->update([
            'code' => $this->code,
            'kanban_category_id' => $this->kanban_category_id,
            'area_id' => $this->area_id,
            'conveyor' => $this->conveyor,
            'family' => $this->family,
            'issue_number' => $this->issue_number,
            'is_active' => $this->is_active,
        ]);

        $this->flashSuccess('Kanban berhasil diperbarui.');
        $this->resetForm();
    }

    public function uploadFile()
    {
        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            sleep(2);
            $import = new KanbanImport();
            Excel::import($import, $this->excelFile);

            if ($import->failures()->isNotEmpty()) {
                session(['import_failures' => $import->failures()]);
            } else {
                session()->forget('import_failures');
            }

            $this->flashSuccess('Data kanban berhasil diupload.');
        } catch (\Exception $e) {
            $this->flashError('Gagal upload data kanban.', $e->getMessage());
        }

        $this->excelFile = null;
    }

    public function render()
    {
        return view('livewire.admin.kanban.index', [
            'kanbans' => Kanban::query()
                ->with(['area', 'kanbanCategory'])
                ->when($this->search, fn($q) => $q->where('code', 'like', '%' . $this->search . '%'))
                ->latest()
                ->paginate(10),
            'areas' => Area::where('is_active', true)->get(),
            'categories' => \App\Models\KanbanCategory::all(),
        ]);
    }
}
