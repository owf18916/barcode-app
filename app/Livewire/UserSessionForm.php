<?php

namespace App\Livewire;

use App\Models\Area;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Session;

class UserSessionForm extends Component
{ 
    public $nik = '';
    public $areas;
    public $area;
    public $selectedArea;
    public $message = '';
    public $suggestions = [];

    public function mount()
    {
        $this->areas = Area::all();

        // Ambil dari session jika ada
        $this->nik = session('nik', '');
        $this->selectedArea = session('area_id') ? Area::find(session('area_id')) : null;

        // Cek timeout: misal, jika lebih dari 5 menit idle, reset session
        if (now()->diffInMinutes(session('last_activity', now())) > 5) {
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

    public function setArea()
    {
        $this->selectedArea = Area::find($this->area);

        session([
            'area_id' => $this->area,
            'last_activity' => now(),
        ]);

        $this->message = 'Area berhasil disimpan.';
    }

    public function render()
    {
        return view('livewire.user-session-form');
    }
}

