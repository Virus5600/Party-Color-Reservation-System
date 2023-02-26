<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\MenuVariation;
use App\Inventory;

class MenuVariationItemFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		return [
			'menu_variation_id' => $this->faker->numberBetween(1, MenuVariation::count()),
			'inventory_id' => $this->faker->numberBetween(1, Inventory::count()),
			'amount' => $this->faker->numberBetween(2, 5)
		];
	}
}