<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Login extends Component
{
    public string $username = '';
    public string $password = '';

    public function mount()
    {
        if (session()->has('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }
    }

    public function login()
    {
        if ($this->username === 'admin' && $this->password === 'nys123') {
            session(['admin_logged_in' => true]);
            return redirect()->route('admin.dashboard');
        }

        $this->addError('username', 'Username atau password salah.');
    }

    public function render()
    {
        return view('livewire.admin.login');
    }
}

