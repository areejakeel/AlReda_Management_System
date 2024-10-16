<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'price' => $this->faker->randomNumber(2),
            'space' => $this->faker->randomNumber(2),
            'user_id' => \App\Models\User::factory(),
            'property_state_id' => \App\Models\PropertyState::factory(),
            'region_id' => \App\Models\Region::factory(),
        ];
    }
}
