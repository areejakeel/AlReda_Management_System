<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Region::class;

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
            'x' => $this->faker->randomNumber(2),
            'y' => $this->faker->randomNumber(2),
            'governorate_id' => \App\Models\Governorate::factory(),
        ];
    }
}
