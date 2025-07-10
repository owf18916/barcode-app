<?php

namespace App\Imports;

use App\Models\Area;
use App\Models\Kanban;
use App\Models\KanbanCategory;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

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

    protected array $codes = [];

    public static $failuresSessionKey = 'kanban_import_failures';

    public function __construct()
    {
        $this->areaMap = Area::pluck('id', 'name')->toArray();
        $this->categoryMap = KanbanCategory::pluck('id', 'name')->toArray();
    }

    public function model(array $row)
    {
        // Skip jika area/category tidak ditemukan
        if (!isset($this->areaMap[$row['area']]) || !isset($this->categoryMap[$row['kanban_category']])) {
            return null;
        }

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
            'code' => ['required', Rule::unique('kanbans', 'code'),
                function ($attribute, $value, $fail) {
                    static $codes = [];

                    if (in_array($value, $codes)) {
                        $fail("Baris duplikat dalam file: $value");
                    }

                    $codes[] = $value;
                },
            ],
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

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                // simpan failures ke cache (karena tidak bisa ke session)
                if ($this->failures()->isNotEmpty()) {
                    cache()->put(self::$failuresSessionKey, $this->failures(), now()->addMinutes(10));
                }
            },
        ];
    }
}


