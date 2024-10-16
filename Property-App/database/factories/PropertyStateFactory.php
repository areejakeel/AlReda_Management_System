<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\PropertyState;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyStateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyState::class;

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
