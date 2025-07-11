<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScanKanban extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'area_id',
        'kanban_id',
        'scanned_kanban',
        'valid_kanban',
        'valid_area',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function kanban()
    {
        return $this->belongsTo(Kanban::class);
    }
}

