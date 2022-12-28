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
		MenuItem::factory()
			->count(random_int(1, 3))
			->create();
	}
}