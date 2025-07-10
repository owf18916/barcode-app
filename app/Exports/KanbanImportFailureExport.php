<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class KanbanImportFailureExport implements FromCollection, WithHeadings
{
    protected Collection $failures;

    public function __construct(Collection $failures)
    {
        $this->failures = $failures;
    }

    public function collection()
    {
        return $this->failures->map(function ($failure) {
            return [
                'Baris Excel' => $failure->row(),
                'Kolom' => $failure->attribute(),
                'Nilai' => data_get($failure->values(), $failure->attribute(), '-'),
                'Pesan Error' => implode(', ', $failure->errors()),
            ];
        });
    }

    public function headings(): array
    {
        return ['Baris Excel', 'Kolom', 'Nilai', 'Pesan Error'];
    }
}

