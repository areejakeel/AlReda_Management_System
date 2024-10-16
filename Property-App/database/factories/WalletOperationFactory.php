<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\WalletOperation;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletOperationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WalletOperation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'value' => $this->faker->randomNumber(2),
            'type' => $this->faker->boolean,
            'describtion' => $this->faker->text,
            'wallet_id' => \App\Models\Wallet::factory(),
        ];
    }
}
