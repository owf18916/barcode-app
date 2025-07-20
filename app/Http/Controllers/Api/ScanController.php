<?php

namespace App\Http\Controllers\Api;

use App\Models\Area;
use App\Models\Kanban;
use App\Models\ScanKanban;
use App\Models\ScanBarcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ScanController extends Controller
{
    public function scanBarcode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'barcode1' => 'required|string|max:255',
            'barcode2' => 'required|string|max:255',
            'barcode3' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        $isMatch = ($request->barcode1 === $request->barcode2 && $request->barcode2 === $request->barcode3);

        $scan = ScanBarcode::create([
            'nik' => $request->nik,
            'area_id' => $request->area_id,
            'barcode1' => $request->barcode1,
            'barcode2' => $request->barcode2,
            'barcode3' => $request->barcode3,
            'is_match' => $isMatch,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Barcode scan recorded.',
            'data' => $scan,
            'is_match' => $isMatch
        ]);
    }

    public function scanKanban(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'scanned_kanban' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        $kanban = Kanban::where('code', $request->scanned_kanban)
                        ->where('is_active', true)
                        ->first();

        $validKanban = false;
        $validArea = false;
        $kanbanId = null;

        if ($kanban) {
            $validKanban = true;
            $kanbanId = $kanban->id;
            // Check if the scanned kanban belongs to the selected area
            if ($kanban->area_id == $request->area_id) {
                $validArea = true;
            }
        }

        $scan = ScanKanban::create([
            'nik' => $request->nik,
            'area_id' => $request->area_id,
            'kanban_id' => $kanbanId,
            'scanned_kanban' => $request->scanned_kanban,
            'valid_kanban' => $validKanban,
            'valid_area' => $validArea,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kanban scan recorded.',
            'data' => $scan,
            'valid_kanban' => $validKanban,
            'valid_area' => $validArea,
        ]);
    }

    /**
     * Memproses batch scan data (barcode dan kanban) yang dikirim dari Server#1.
     * Menggunakan mass insertion untuk performa optimal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processBatchScans(Request $request)
    {
        $scans = $request->json()->all(); // Ambil seluruh array JSON dari request body

        if (empty($scans) || !is_array($scans)) {
            return response()->json(['success' => false, 'message' => 'Invalid or empty batch data provided.'], 400);
        }

        $results = []; // Array untuk menyimpan hasil proses setiap item scan (untuk feedback ke Server#1)
        
        $barcodesToInsert = []; // Array untuk menampung data barcode yang akan di-insert secara massal
        $kanbansToInsert = [];  // Array untuk menampung data kanban yang akan di-insert secara massal

        // Collect current timestamps for 'created_at' and 'updated_at' if your models use them automatically
        $now = now(); // Carbon instance for current timestamp

        foreach ($scans as $scanData) {
            $localLogId = $scanData['local_log_id'] ?? null;

            if (!isset($scanData['nik'], $scanData['area_id'], $scanData['scan_type'], $scanData['scan_timestamp'])) {
                $results[] = [
                    'local_log_id' => $localLogId,
                    'success' => false,
                    'message' => 'Missing essential fields (nik, area_id, scan_type, scan_timestamp) for one scan item.'
                ];
                continue;
            }

            try {
                if ($scanData['scan_type'] === 'barcode') {
                    if (!isset($scanData['barcode1'], $scanData['barcode2'], $scanData['barcode3'])) {
                        $results[] = ['local_log_id' => $localLogId, 'success' => false, 'message' => 'Missing barcode specific fields (barcode1, barcode2, barcode3).'];
                        continue;
                    }
                    
                    // Siapkan data untuk mass insertion ScanBarcode
                    $barcodesToInsert[] = [
                        'nik' => $scanData['nik'],
                        'area_id' => $scanData['area_id'],
                        'barcode1' => $scanData['barcode1'],
                        'barcode2' => $scanData['barcode2'],
                        'barcode3' => $scanData['barcode3'],
                        'is_match' => $scanData['is_match_local'] ?? false,
                        'scanned_at' => $scanData['scan_timestamp'], // Gunakan timestamp asli dari client
                        'created_at' => $now,
                    ];
                    
                    $results[] = ['local_log_id' => $localLogId, 'success' => true, 'message' => 'Barcode data collected for batch insert.'];

                } elseif ($scanData['scan_type'] === 'kanban') {
                    if (!isset($scanData['kanban_code'])) {
                        $results[] = ['local_log_id' => $localLogId, 'success' => false, 'message' => 'Missing kanban specific field (kanban_code).'];
                        continue;
                    }
                    
                    // Siapkan data untuk mass insertion ScanKanban
                    $kanbansToInsert[] = [
                        'nik' => $scanData['nik'],
                        'area_id' => $scanData['area_id'],
                        'kanban_id' => $scanData['kanban_id'],
                        'scanned_kanban' => $scanData['kanban_code'],
                        'valid_kanban' => $scanData['valid_kanban_local'] ?? false,
                        'valid_area' => $scanData['valid_area_local'] ?? false,
                        'scanned_at' => $scanData['scan_timestamp'],
                        'created_at' => $now,
                    ];
                    
                    $results[] = ['local_log_id' => $localLogId, 'success' => true, 'message' => 'Kanban data collected for batch insert.'];

                } else {
                    $results[] = ['local_log_id' => $localLogId, 'success' => false, 'message' => 'Unknown scan type: ' . ($scanData['scan_type'] ?? 'N/A')];
                }

            } catch (\Exception $e) {
                Log::error("Error collecting batch scan data for local ID {$localLogId}: " . $e->getMessage());
                $results[] = [
                    'local_log_id' => $localLogId,
                    'success' => false,
                    'message' => 'Internal server error collecting data for this item: ' . $e->getMessage()
                ];
            }
        }

        // --- Lakukan Mass Insertion di luar loop ---
        try {
            // Insert barcodes jika ada data
            if (!empty($barcodesToInsert)) {
                ScanBarcode::insert($barcodesToInsert);
            }

            // Insert kanbans jika ada data
            if (!empty($kanbansToInsert)) {
                ScanKanban::insert($kanbansToInsert);
            }

            Log::info('scan-insertion', ['barcode' => $barcodesToInsert, 'kanban' => $kanbansToInsert]);

        } catch (\Exception $e) {
            Log::error("Error performing mass insertion for scans: " . $e->getMessage());
            // Jika mass insertion gagal, kita perlu menandai semua item di 'results' sebagai gagal
            // Ini akan memerlukan penyesuaian pada 'results' array
            foreach ($results as &$result) { // Gunakan referensi untuk memodifikasi array asli
                if ($result['success']) { // Hanya ubah yang tadinya sukses dikumpulkan
                    $result['success'] = false;
                    $result['message'] = 'Failed to save due to mass insertion error: ' . $e->getMessage();
                }
            }
            unset($result); // Lepaskan referensi
            return response()->json(['success' => false, 'message' => 'Critical error during batch database insertion: ' . $e->getMessage(), 'results' => $results], 500);
        }

        return response()->json(['success' => true, 'message' => 'Batch processing complete and data saved.', 'results' => $results]);
    }
}