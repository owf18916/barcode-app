<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            'Welding',
            'Painting',
            'Assembly',
            'Inspection',
            'Packaging',
        ];

        foreach ($areas as $name) {
            Area::updateOrCreate(['name' => $name]);
        }
    }
}