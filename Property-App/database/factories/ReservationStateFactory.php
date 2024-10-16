<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\ReservationState;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationStateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReservationState::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name_ar' => $this->faker->text(255),
            'name_en' => $this->faker->text(255),
        ];
    }
}
