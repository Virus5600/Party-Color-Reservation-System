<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Inventory;

class InventoryFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		$quantity = $this->faker->numberBetween(0, 50);

		return [
			'item_name' => $this->faker->word(),
			'quantity' => $quantity,
			'measurement_unit' => $this->faker->randomElement(['kg', 'g']),
			'deleted_at' => $quantity > 0 ? null : now()
		];
	}
}