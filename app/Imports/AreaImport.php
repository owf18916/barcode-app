<?php

namespace App\Imports;

use App\Models\Area;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AreaImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    WithEvents
{
    use Importable, SkipsFailures;

    public int $successCount = 0;

    protected array $names = [];

    public function model(array $row)
    {
        $this->successCount++;

        return new Area([
            'name' => $row['name'],
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('areas', 'name'),
                function ($attribute, $value, $fail) {
                    static $names = [];

                    if (in_array($value, $names)) {
                        $fail("Baris duplikat dalam file: $value");
                    }

                    $names[] = $value;
                },
            ],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'name.required' => 'Nama area wajib diisi.',
            'name.unique'   => 'Nama area sudah ada.',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function () {
                session()->flash('import_success_count', $this->successCount);
                session()->flash('import_failures', $this->failures()); // gunakan method bawaan
            },
        ];
    }
}

