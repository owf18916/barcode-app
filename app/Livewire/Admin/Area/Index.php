<?php

namespace App\Livewire\Admin\Area;

use App\Models\Area;
use Livewire\Component;
use App\Imports\AreaImport;
use App\Traits\Swalable;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination, WithFileUploads, Swalable;

    public string $name = '';
    public bool $is_active = true;
    public $areaIdBeingEdited = null;
    public $excelFile;
    public string $search = '';

    public function save()
    {
        $this->validate([
            'name' => 'required|unique:areas,name',
        ]);

        Area::create([
            'name' => $this->name,
            'is_active' => $this->is_active,
        ]);

        $this->resetForm();
        $this->flashSuccess('Area berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $area = Area::findOrFail($id);
        $this->name = $area->name;
        $this->is_active = $area->is_active;
        $this->areaIdBeingEdited = $area->id;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|unique:areas,name,' . $this->areaIdBeingEdited,
        ]);

        $area = Area::findOrFail($this->areaIdBeingEdited);
        
        $area->update([
            'name' => $this->name,
            'is_active' => $this->is_active,
        ]);

        $this->resetForm();

        $this->flashSuccess('Data area berhasil diupdate.');
    }

    public function resetForm()
    {
        $this->reset(['name', 'is_active', 'areaIdBeingEdited']);
    }

    public function uploadFile()
    {
        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            sleep(2);

            $import = new AreaImport;
            Excel::import($import, $this->excelFile);

            $failures = $import->failures();

            if ($failures->isNotEmpty()) {
                session()->flash('import_failures', $failures);
                session()->flash('import_success_count', $import->successCount ?? 0);

                $this->flashError(
                    'Beberapa data gagal diupload.',
                    'Silakan periksa daftar kesalahan di bawah.'
                );
            } else {
                $this->flashSuccess('Semua data area berhasil diupload.');
            }
        } catch (\Exception $e) {
            $this->flashError('Data area gagal diupload.', $e->getMessage());
        }

        $this->excelFile = null;
    }

    public function applySearch()
    {
        $this->resetPage(); // Reset ke halaman 1 saat search berubah
    }

    public function render()
    {
        $areas = Area::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.area.index', compact('areas'));
    }
}
