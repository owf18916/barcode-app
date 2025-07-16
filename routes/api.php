<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\ScanController;

// Public API routes for Server#1
Route::get('/areas', [MasterDataController::class, 'getAreas']);
Route::get('/master-kanbans', [MasterDataController::class, 'getMasterKanbans']);

Route::post('scan/barcode', [ScanController::class, 'scanBarcode']);
Route::post('scan/kanban', [ScanController::class, 'scanKanban']);

Route::post('/scan/batch', [ScanController::class, 'processBatchScans']);