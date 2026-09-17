<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Game;
use App\Models\Hall;
use App\Models\Team;
use App\Models\Timeslot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    protected $model = Game::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_a_id'      => Team::factory(),
            'team_b_id'      => Team::factory(),
            'category_id'    => Category::factory(),
            'hall_id'        => Hall::factory(),
            'timeslot_id'    => Timeslot::factory(),
            'finale_type'    => null,
            'play_for_third' => false,
            'points_team_a'  => 0,
            'points_team_b'  => 0,
            'played'         => false,
            'temporary'      => false,
        ];
    }

    /**
     * Indicate that the game is played with scores.
     */
    public function played(int $pointsA = 10, int $pointsB = 8): static
    {
        return $this->state(fn (array $attributes) => [
            'played'        => true,
            'points_team_a' => $pointsA,
            'points_team_b' => $pointsB,
        ]);
    }

    /**
     * Indicate that the game is temporary.
     */
    public function temporary(): static
    {
        return $this->state(fn (array $attributes) => [
            'temporary' => true,
        ]);
    }

    /**
     * Indicate that the game is a finale game.
     */
    public function finale(int $finaleType = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'finale_type' => $finaleType,
        ]);
    }
}
