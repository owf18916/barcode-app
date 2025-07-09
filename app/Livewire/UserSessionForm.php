<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Area;
use Illuminate\Support\Facades\Session;

class UserSessionForm extends Component
{
    public $nik = '';
    public $areaSearch = '';
    public $selectedArea;
    public $areaSearchInput = ''; // untuk input real-time
    public $message = '';

    public function mount()
    {
        // Ambil dari session jika ada
        $this->nik = session('nik', '');
        $this->selectedArea = session('area_id') ? Area::find(session('area_id')) : null;

        // Cek timeout: misal, jika lebih dari 10 menit idle, reset session
        if (now()->diffInMinutes(session('last_activity', now())) > 10) {
            session()->forget(['nik', 'area_id', 'last_activity']);
            $this->nik = '';
            $this->selectedArea = null;
            $this->message = 'Session expired. Please re-enter NIK and Area.';
        }
    }

    public function setNik()
    {
        session(['nik' => $this->nik, 'last_activity' => now()]);
        $this->message = 'NIK berhasil disimpan.';
    }

    public function selectArea($id)
    {
        $area = Area::find($id);

        if ($area) {
            $this->selectedArea = $area;
            $this->areaSearchInput = $area->name;
            $this->areaSearch = $area->name;

            session([
                'area_id' => $area->id,
                'last_activity' => now(),
            ]);

            $this->message = 'Area berhasil disimpan.';
        } else {
            $this->addError('areaSearchInput', 'Area tidak ditemukan.');
        }
    }

    public function render()
    {
        $suggestions = [];

        if (strlen($this->areaSearchInput) >= 2 && !$this->selectedArea) {
            $suggestions = Area::where('name', 'like', '%' . $this->areaSearchInput . '%')->limit(10)->get();
        }

        return view('livewire.user-session-form', [
            'suggestions' => $suggestions
        ]);
    }
}

