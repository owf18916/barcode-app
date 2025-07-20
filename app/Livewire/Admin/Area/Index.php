<?php

namespace App\Livewire\Admin\Area;

use App\Models\Area;
use Livewire\Component;
use App\Traits\Swalable;
use App\Imports\AreaImport;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Services\WebhookService;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination, WithFileUploads, Swalable;

    public string $name = '';
    public bool $is_active = true;
    public $areaIdBeingEdited = null;
    public $excelFile;
    public string $search = '';

    protected WebhookService $webhookService;

    public function boot(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

        // --- PERUBAHAN DI SINI UNTUK SINKRONISASI MANUAL ---
    public function syncAreasToLocalServer()
    {
        try {
            $pendingAreas = $this->webhookService->getUnsyncedMasterRecords('area');
            
            if ($pendingAreas->isEmpty()) {
                $this->flashInfo('Tidak ada data Area yang perlu disinkronkan.');
                return;
            }

            // Kumpulkan semua data yang perlu disinkronkan ke dalam satu array
            $dataToSend = $pendingAreas->map(fn($area) => $area->toArray())->all();
            
            // Kirim semua data dalam satu request batch
            $success = $this->webhookService->sendMasterBatchUpdate('area', 'batch_upsert', $dataToSend);

            if ($success) {
                // Jika pengiriman batch berhasil, tandai semua record sebagai synced
                $syncedCount = 0;
                foreach ($pendingAreas as $area) {
                    if ($this->webhookService->markMasterRecordAsSynced('area', $area->id)) {
                        $syncedCount++;
                    } else {
                        \Illuminate\Support\Facades\Log::warning("Failed to mark Area ID: {$area->id} as synced after successful batch webhook.");
                    }
                }
                $this->flashSuccess("{$syncedCount} Area berhasil disinkronkan ke Server #1.");
            } else {
                $this->flashError("Gagal sinkronisasi Area ke Server #1.", "Periksa log untuk detail lebih lanjut.");
            }
        } catch (\Exception $e) {
            $this->flashError("Terjadi kesalahan umum saat sinkronisasi Area.", $e->getMessage());
            \Illuminate\Support\Facades\Log::error("Manual Sync Pending Areas Batch Error: " . $e->getMessage());
        }
    }

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
