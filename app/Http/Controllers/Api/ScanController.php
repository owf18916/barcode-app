<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScanBarcode;
use App\Models\ScanKanban;
use App\Models\Kanban;
use App\Models\Area;
use Illuminate\Http\Request;
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
}