<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\MenuItem;

class MenuItemTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		MenuItem::create([
			'menu_id' => 1,
			'inventory_id' => 1,
			'amount' => 5
		]);

		MenuItem::create([
			'menu_id' => 1,
			'inventory_id' => 2,
			'amount' => 5
		]);

		MenuItem::create([
			'menu_id' => 1,
			'inventory_id' => 3,
			'amount' => 5
		]);

		MenuItem::create([
			'menu_id' => 2,
			'inventory_id' => 4,
			'amount' => 5
		]);

		MenuItem::create([
			'menu_id' => 2,
			'inventory_id' => 5,
			'amount' => 5
		]);
	}
}