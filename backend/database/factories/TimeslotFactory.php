<?php

namespace Database\Factories;

use App\Models\Timeslot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Timeslot>
 */
class TimeslotFactory extends Factory
{
    protected $model = Timeslot::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'start_time' => '10:00',
            'end_time'   => '10:20',
            'temporary'  => false,
        ];
    }

    /**
     * Indicate that the timeslot is temporary.
     */
    public function temporary(): static
    {
        return $this->state(fn (array $attributes) => [
            'temporary' => true,
        ]);
    }
}
