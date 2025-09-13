<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\User;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'seller_id' => User::factory(),
            'item_name' => $this->faker->word,
            'price'     => $this->faker->numberBetween(500, 5000),
            'is_sold'   => false,
            'image_path' => 'noimage.png',
            'brand_name'     => $this->faker->company,
            'condition' => 1,
            'description' => $this->faker->sentence,
        ];
    }
}
