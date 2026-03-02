<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            [
                'name' => 'Submitted and Await for Admin Response',
                'step_number' => 1,
                'color' => '#28a745',

            ],
            [
                'name' => 'Send to Respondent for Response',
                'step_number' => 2,
                'color' => '#ffc107',

            ],
            [
                'name' => 'Respond by respondent',
                'step_number' => 3,
                'color' => '#17a2b8',

            ],
            [
                'name' => 'Resolved',
                'step_number' => 4,
                'color' => '#007bff',

            ],
        ];

        foreach ($stages as $stage) {
            \App\Models\Stage::create($stage);
        }
    }
}
