<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\ScanController;

// Public API routes for Server#1
Route::get('areas', [AreaController::class, 'index']);
Route::post('scan/barcode', [ScanController::class, 'scanBarcode']);
Route::post('scan/kanban', [ScanController::class, 'scanKanban']);