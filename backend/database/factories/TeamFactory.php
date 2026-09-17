<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Section;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'section_id' => Section::factory(),
            'category_id' => Category::factory(),
            'dummy' => false,
            'temporary' => false,
        ];
    }

    /**
     * Indicate that the team is a dummy placeholder.
     */
    public function dummy(): static
    {
        return $this->state(fn (array $attributes) => [
            'dummy' => true,
        ]);
    }
}
