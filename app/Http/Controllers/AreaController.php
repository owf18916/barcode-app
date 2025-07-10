<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AreaController extends Controller
{
    public function exportErrors()
    {
        $failures = session('import_failures', []);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->fromArray(['Baris', 'Pesan Error'], null, 'A1');

        $rowIndex = 2;
        foreach ($failures as $failure) {
            $sheet->setCellValue("A{$rowIndex}", $failure->row());
            $sheet->setCellValue("B{$rowIndex}", implode(', ', $failure->errors()));
            $rowIndex++;
        }

        $filename = 'import-errors-' . now()->timestamp . '.xlsx';
        $filepath = storage_path("app/public/{$filename}");

        (new Xlsx($spreadsheet))->save($filepath);

        return response()->download($filepath)->deleteFileAfterSend(true);
    }
}
