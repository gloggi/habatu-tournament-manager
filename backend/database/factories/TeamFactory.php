<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'section_id' => Section::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,  // Pick a random existing category
        ];
    }
}
