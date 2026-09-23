<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\ReportReason::firstOrCreate(['nombre' => 'Contenido ofensivo o inapropiado']);
        \App\Models\ReportReason::firstOrCreate(['nombre' => 'Falsificación o fraude']);
        \App\Models\ReportReason::firstOrCreate(['nombre' => 'Spam']);
        \App\Models\ReportReason::firstOrCreate(['nombre' => 'Categoría incorrecta']);
    }
}
