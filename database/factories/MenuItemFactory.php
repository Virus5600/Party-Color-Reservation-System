<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Menu;
use App\Inventory;

class MenuItemFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		return [
			'menu_id' => $this->faker->numberBetween(1, Menu::count()),
			'inventory_id' => $this->faker->numberBetween(1, Inventory::count()),
			'amount' => $this->faker->numberBetween(2, 5)
		];
	}
}