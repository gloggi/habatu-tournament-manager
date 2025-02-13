<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Option;


class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Option::create([
            'tournament_name' => 'HaBaTu',
            'start_time' => '09:00',
            'game_duration' => 10,
            'break_duration' => 5,
            'additional_slots' => 5,
            'started_tournament' => false,
            'ended_round_games' => false,
        ]);
    }
}
