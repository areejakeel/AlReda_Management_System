<?php

namespace Database\Factories;

use App\Models\Report;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Report::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->text,
            'user_id' => \App\Models\User::factory(),
            'property_id' => \App\Models\Property::factory(),
        ];
    }
}
