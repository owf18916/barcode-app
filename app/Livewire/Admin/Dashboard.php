<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Dashboard extends Component
{
    public function logout()
    {
        session()->forget('admin_logged_in');
        
        return redirect()->route('admin.login');
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}

