<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kanban;

class KanbanSeeder extends Seeder
{
    public function run(): void
    {
        $kanbans = [
            ['code' => 'KBN-001', 'is_active' => true],
            ['code' => 'KBN-002', 'is_active' => true],
            ['code' => 'KBN-003', 'is_active' => false],
            ['code' => 'KBN-004', 'is_active' => true],
            ['code' => 'KBN-005', 'is_active' => false],
        ];

        foreach ($kanbans as $data) {
            Kanban::updateOrCreate(['code' => $data['code']], $data);
        }
    }
}

