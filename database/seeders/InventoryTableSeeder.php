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
		Inventory::factory()
			->count(5)
			->create();
	}
}