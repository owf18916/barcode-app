<?php

use App\Livewire\Home;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::get('/', Home::class);
    Route::get('/admin/login', \App\Livewire\Admin\Login::class)->name('admin.login');
});

Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');
    Route::get('/area', \App\Livewire\Admin\Area\Index::class)->name('admin.area');
    Route::get('/kanban', \App\Livewire\Admin\Kanban\Index::class)->name('admin.kanban');
    Route::get('/scan-results', \App\Livewire\Admin\ScanResults::class)->name('admin.scan-results');
});


