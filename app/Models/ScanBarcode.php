<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScanBarcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'area_id',
        'barcode1',
        'barcode2',
        'barcode3',
        'is_match',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}

