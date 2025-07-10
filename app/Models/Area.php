<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Area extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scanBarcodes()
    {
        return $this->hasMany(ScanBarcode::class);
    }

    public function scanKanbans()
    {
        return $this->hasMany(ScanKanban::class);
    }
}

