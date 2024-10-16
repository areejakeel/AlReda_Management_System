<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'start_date' => $this->faker->date,
            'end_date' => $this->faker->date,
            'price' => $this->faker->randomNumber(2),
            'property_id' => \App\Models\Property::factory(),
            'user_id' => \App\Models\User::factory(),
            'reservation_state_id' => \App\Models\ReservationState::factory(),
            'reservation_type_id' => \App\Models\ReservationType::factory(),
        ];
    }
}
