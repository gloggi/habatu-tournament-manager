<?php

namespace Database\Factories;

use App\Models\Option;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Option>
 */
class OptionFactory extends Factory
{
    protected $model = Option::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tournament_name'    => 'Test Tournament',
            'start_time'         => '08:00',
            'game_duration'      => 20,
            'break_duration'     => 5,
            'additional_slots'   => 0,
            'started_tournament' => false,
            'ended_round_games'  => false,
            'round_robin'        => true,
            'group_phase'        => false,
            'play_for_third_place' => false,
        ];
    }
}
