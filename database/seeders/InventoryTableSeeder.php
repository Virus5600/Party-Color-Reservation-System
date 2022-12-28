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
			'item_name' => 'pork',
			'quantity' => 50,
			'measurement_unit' => 'kg'
		]);

		Inventory::create([
			'item_name' => 'beef',
			'quantity' => 50,
			'measurement_unit' => 'kg'
		]);

		Inventory::create([
			'item_name' => 'chicken',
			'quantity' => 50,
			'measurement_unit' => 'kg'
		]);

		Inventory::create([
			'item_name' => 'coke',
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