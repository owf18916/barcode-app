<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kanban extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'kanban_category_id', 'area_id', 'issue_number', 'family', 'conveyor', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scanKanbans()
    {
        return $this->hasMany(ScanKanban::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function kanbanCategory(): BelongsTo
    {
        return $this->belongsTo(KanbanCategory::class);
    }
}

