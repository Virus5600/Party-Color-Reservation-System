<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Menu;

class MenuTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Menu::factory()
			->count(2)
			->create();
	}
}