<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Inventory;

class InventoryTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Inventory::create([
			'item_name' => 'Pork',
			'quantity' => 50,
			'measurement_unit' => 'kg'
		]);

		Inventory::create([
			'item_name' => 'Beef',
			'quantity' => 50,
			'measurement_unit' => 'kg'
		]);

		Inventory::create([
			'item_name' => 'Chicken',
			'quantity' => 50,
			'measurement_unit' => 'kg'
		]);

		Inventory::create([
			'item_name' => 'Coke',
			'quantity' => 10,
			'measurement_unit' => 'L'
		]);

		Inventory::create([
			'item_name' => 'Iced Tea',
			'quantity' => 10,
			'measurement_unit' => 'L'
		]);
	}
}