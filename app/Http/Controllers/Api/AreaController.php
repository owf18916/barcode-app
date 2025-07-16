<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::where('is_active', true)->get();
        return response()->json([
            'success' => true,
            'message' => 'Areas retrieved successfully.',
            'data' => $areas
        ]);
    }
}