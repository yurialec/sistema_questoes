<?php

namespace Database\Seeders;

use App\Models\Ano;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $anos = [
            2020,
            2021,
            2022,
            2023,
            2024,
            2025,
            2026,
        ];

        foreach ($anos as $ano) {
            Ano::firstOrCreate(['ano' => $ano]);
        }
    }
}
