<?php

namespace App\Imports;

use App\Models\Area;
use App\Models\Kanban;
use App\Models\KanbanCategory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\Importable;

class KanbanImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    WithChunkReading,
    WithBatchInserts,
    ShouldQueue
{
    use Importable, SkipsFailures;

    protected array $areaMap;
    protected array $categoryMap;

    public function __construct()
    {
        $this->areaMap = Area::pluck('id', 'name')->toArray();
        $this->categoryMap = KanbanCategory::pluck('id', 'name')->toArray();
    }

    public function model(array $row)
    {
        return new Kanban([
            'code' => $row['code'],
            'kanban_category_id' => $this->categoryMap[$row['kanban_category']] ?? null,
            'area_id' => $this->areaMap[$row['area']] ?? null,
            'conveyor' => $row['conveyor'] ?? null,
            'family' => $row['family'] ?? null,
            'issue_number' => $row['issue_number'] ?? null,
            'is_active' => in_array((string) $row['is_active'], ['1', 'true', 'yes'], true),
        ]);
    }

    public function rules(): array
    {
        return [
            'code' => ['required', Rule::unique('kanbans', 'code')],
            'kanban_category' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!array_key_exists($value, $this->categoryMap)) {
                        $fail("Kategori Kanban \"$value\" tidak ditemukan.");
                    }
                }
            ],
            'area' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!array_key_exists($value, $this->areaMap)) {
                        $fail("Area \"$value\" tidak ditemukan.");
                    }
                }
            ],
            'family' => ['required'],
            'issue_number' => ['required'],
            'is_active' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!in_array((string) $value, ['0', '1', 'true', 'false', 'yes', 'no'], true)) {
                        $fail("Kolom aktif harus bernilai 1 atau 0.");
                    }
                }
            ]
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'code.required' => 'Kode kanban wajib diisi.',
            'code.unique' => 'Kode kanban sudah digunakan.',
            'kanban_category.required' => 'Kategori kanban wajib diisi.',
            'area.required' => 'Area wajib diisi.',
            'family' => 'Family wajib diisi.',
            'issue_number' => 'Issue number wajib diisi.',
            'is_active.required' => 'Status aktif wajib diisi (1/0).',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 500;
    }
}


