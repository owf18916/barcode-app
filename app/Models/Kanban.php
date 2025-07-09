<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kanban extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'is_active'];

    public function scanKanbans()
    {
        return $this->hasMany(ScanKanban::class);
    }
}

