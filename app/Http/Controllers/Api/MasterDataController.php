<?php

namespace App\Http\Controllers\Api;

use App\Models\Kanban;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Area; // Pastikan model Anda sesuai
use App\Models\MasterKanban; // Sesuaikan dengan model master kanban Anda
use App\Models\MasterBarcode; // Sesuaikan dengan model master barcode Anda

class MasterDataController extends Controller
{
    public function getAreas()
    {
        $areas = Area::all(); // Ambil semua area
        return response()->json(['success' => true, 'data' => $areas]);
    }

    public function getMasterKanbans()
    {
        // Ambil semua kanban aktif atau sesuai kebutuhan. Pastikan ada area_id dan status.
        $kanbans = Kanban::select(['code', 'area_id', 'is_active'])->get(); // Sesuaikan query jika perlu filter/join
        return response()->json(['success' => true, 'data' => $kanbans]);
    }
}