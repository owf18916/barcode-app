<?php

namespace App\Traits;

trait Swalable {
    public function flashSuccess($message, $footer = null) {
        $this->setupFlash("Sukses", $message, 'success', $footer);
    }

    public function flashError($message, $footer = null) {
        $this->setupFlash("Gagal", $message, 'error', $footer);
    }

    public function flashInfo($message, $footer = null) {
        $this->setupFlash("Info", $message, 'info', $footer);
    }

    public function toastSuccess($title) {
        $this->setupToast('success', $title);
    }

    public function toastInfo($title) {
        $this->setupToast('success', $title);
    }

    public function toastError($title) {
        $this->setupToast('info', $title);
    }

    private function setupFlash($title, $message, $type, $footer = null) {
        $this->dispatch('swal-fired', [
            'title' => $title,
            'type' => $type,
            'message' => $message,
            'footer' => $footer
        ]);
    }

    private function setupToast($icon, $title) {
        $this->dispatch('toast-fired', [
            'icon' => $icon,
            'title' => $title
        ]);
    }
}