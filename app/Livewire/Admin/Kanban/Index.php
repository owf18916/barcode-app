<?php

namespace App\Livewire\Admin\Kanban;

use App\Models\Area;
use App\Models\Kanban;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\WithPagination;
use App\Imports\KanbanImport;
use Livewire\WithFileUploads;
use App\Models\KanbanCategory;
use App\Services\WebhookService;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Attributes\On;

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

    public bool $isImporting = false;
    public bool $importFinished = false;

    protected WebhookService $webhookService; // Declare the property

    // Inject the service via constructor
    public function boot(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    // --- PERUBAHAN DI SINI UNTUK SINKRONISASI MANUAL ---
    #[On('syncKanbansToLocalServer')]
    public function syncKanbansToLocalServer()
    {
        try {
            $pendingKanbans = $this->webhookService->getUnsyncedMasterRecords('kanban');
            
            if ($pendingKanbans->isEmpty()) {
                $this->flashInfo('Tidak ada data Kanban yang perlu disinkronkan.');
                return;
            }

            // Kumpulkan semua data yang perlu disinkronkan ke dalam satu array
            $dataToSend = $pendingKanbans->map(fn($kanban) => $kanban->toArray())->all();
            
            // Kirim semua data dalam satu request batch
            $success = $this->webhookService->sendMasterBatchUpdate('kanban', 'batch_upsert', $dataToSend);

            if ($success) {
                // Jika pengiriman batch berhasil, tandai semua record sebagai synced
                $kanbanIds = $pendingKanbans->pluck('id')->all();

                $updatedCount = \App\Models\Kanban::whereIn('id', $kanbanIds)
                    ->update(['synced_at' => now()]);

                $this->flashSuccess("{$updatedCount} Kanban berhasil disinkronkan ke Server #1.");
            } else {
                $this->flashError("Gagal sinkronisasi Kanban ke Server #1.", "Periksa log untuk detail lebih lanjut.");
            }
        } catch (\Exception $e) {
            $this->flashError("Terjadi kesalahan umum saat sinkronisasi Kanban.", $e->getMessage());
            \Illuminate\Support\Facades\Log::error("Manual Sync Pending Kanbans Batch Error: " . $e->getMessage());
        }
    }

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
            $import = new KanbanImport();
            Excel::import($import, $this->excelFile);

            if ($import->failures()->isNotEmpty()) {
                session(['import_failures' => $import->failures()]);
            } else {
                session()->forget('import_failures');
            }

            $this->flashSuccess('Data kanban sedang diproses import ke database. Silahkan tunggu notifikasi selanjutnya.');
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
