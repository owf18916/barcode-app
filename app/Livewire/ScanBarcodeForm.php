<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ScanBarcode;
use App\Traits\Swalable;

class ScanBarcodeForm extends Component
{
    use Swalable;

    public string $barcode1 = '', $barcode2 = '', $barcode3 = '';
    public ?bool $result = null;
    public string $message = '';

    public function updated($field) {
        if ($this->barcode1 && $this->barcode2 && $this->barcode3) {
            $this->checkBarcodes();
        }
    }

    public function checkBarcodes()
    {
        $this->result = $this->barcode1 === $this->barcode2 && $this->barcode2 === $this->barcode3;
        
        $this->message = $this->result
            ? '✅ Semua barcode cocok'
            : '❌ Barcode tidak cocok';

        ScanBarcode::create([
            'nik' => session('nik'),
            'area_id' => session('area_id'),
            'barcode1' => $this->barcode1,
            'barcode2' => $this->barcode2,
            'barcode3' => $this->barcode3,
            'is_match' => $this->result,
        ]);

        if ($this->result) {
            $this->toastSuccess($this->message);
        } else {
            $this->toastError($this->message);
        }

        // ✅ Livewire 3 native dispatch
        $this->dispatch('play-sound', res: $this->result);
        $this->dispatch('refocus-barcode');

        // reset setelah dispatch
        $this->reset(['barcode1', 'barcode2', 'barcode3', 'result', 'message']);
    }

    public function render()
    {
        return view('livewire.scan-barcode-form');
    }
}
